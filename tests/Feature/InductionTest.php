<?php

namespace Tests\Feature;

use App\Mail\InductionContentMail;
use App\Mail\InductionReminderMail;
use App\Models\Page;
use App\Models\Question;
use App\Models\QuestionUserResponse;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class InductionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:',
            'session.driver' => 'array', 'cache.default' => 'array', 'mail.default' => 'array']);
        DB::purge('sqlite');
        // Base efímera de prueba, sin ejecutar ni crear migraciones.
        $tables = [
            'users' => 'id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT, email TEXT UNIQUE, password TEXT, role TEXT, tipo_usuario TEXT, area TEXT, puesto TEXT, created_at TEXT, updated_at TEXT, deleted_at TEXT, email_verified_at TEXT, remember_token TEXT',
            'pages' => 'id INTEGER PRIMARY KEY AUTOINCREMENT, title TEXT, slug TEXT UNIQUE, content TEXT, destinado_a TEXT, video_url TEXT, video_file TEXT, attachment TEXT, "order" INTEGER DEFAULT 0, created_at TEXT, updated_at TEXT',
            'questions' => 'id INTEGER PRIMARY KEY AUTOINCREMENT, page_id INTEGER, question_text TEXT, question_type TEXT, options TEXT, correct_option TEXT, "order" INTEGER DEFAULT 0, is_active INTEGER DEFAULT 1, created_at TEXT, updated_at TEXT',
            'question_user_responses' => 'id INTEGER PRIMARY KEY AUTOINCREMENT, question_id INTEGER, user_id INTEGER, answer TEXT, is_correct INTEGER, created_at TEXT, updated_at TEXT, UNIQUE(user_id, question_id)',
        ];
        foreach (['page_user', 'induction_page_user', 'user_page_views'] as $pivot) {
            $tables[$pivot] = 'id INTEGER PRIMARY KEY AUTOINCREMENT, page_id INTEGER, user_id INTEGER, created_at TEXT, updated_at TEXT, UNIQUE(user_id, page_id)';
        }
        foreach ($tables as $name => $columns) DB::statement("CREATE TABLE $name ($columns)");
    }

    private function user(array $attributes = []): User
    {
        return User::create(array_merge(['name' => 'Participante', 'email' => uniqid().'@example.test',
            'password' => 'password', 'role' => 'user', 'tipo_usuario' => 'Colaborador', 'area' => 'Capital Humano'], $attributes));
    }

    private function page(array $attributes = []): Page
    {
        return Page::create(array_merge(['title' => 'Módulo local', 'slug' => uniqid('modulo-'),
            'destinado_a' => ['Ambos'], 'order' => 1], $attributes));
    }

    private function question(Page $page, array $attributes = []): Question
    {
        $question = new Question(array_merge(['question_text' => 'Pregunta', 'question_type' => 'opcion_multiple',
            'options' => json_encode(['Sí', 'No']), 'correct_option' => '0', 'is_active' => 1], $attributes));
        $question->page_id = $page->id;
        $question->save();
        return $question;
    }

    public function test_participant_cannot_use_administrative_routes(): void
    {
        $user = $this->user();
        $page = $this->page();
        $this->actingAs($user)->get('/admin')->assertForbidden();
        $this->get('/users')->assertForbidden();
        $this->get('/admin/reportes')->assertForbidden();
        $this->post('/pages', [])->assertForbidden();
        $this->post('/admin/send-reminder/'.$user->id)->assertForbidden();
        $this->post('/admin/notify-content/'.$page->id)->assertForbidden();
        $this->get('/lobby')->assertOk();
    }

    public function test_admin_panel_and_reports_render_metrics(): void
    {
        $this->user();
        $admin = $this->user(['role' => 'admin']);
        $this->actingAs($admin)->get('/admin')->assertOk()->assertSee('Participantes activos');
        $this->get('/admin/reportes')->assertOk()->assertSee('Estado de las inducciones');
        $this->get('/pages')->assertOk();
    }

    public function test_destinations_accept_json_legacy_text_and_accents(): void
    {
        $user = $this->user(['tipo_usuario' => 'Estadía']);
        $page = $this->page(['destinado_a' => ['Estadia']]);
        $this->assertTrue($page->fresh()->accessibleBy($user));
        DB::table('pages')->where('id', $page->id)->update(['destinado_a' => 'Ambos']);
        $this->assertTrue($page->fresh()->accessibleBy($user));
        $other = $this->page(['destinado_a' => ['Capital Humano']]);
        $this->assertFalse($other->accessibleBy($user));
        $this->assertTrue($other->accessibleBy($this->user()));
    }

    public function test_viewed_module_with_questions_is_not_completed_until_answered(): void
    {
        $user = $this->user();
        $first = $this->page();
        $second = $this->page(['order' => 2]);
        $question = $this->question($second);
        $user->inductionViewed()->attach([$first->id, $second->id]);
        $this->assertSame(50, $user->induction_viewed_percentage);
        QuestionUserResponse::create(['user_id' => $user->id, 'question_id' => $question->id, 'answer' => 'Sí', 'is_correct' => 1]);
        $this->assertSame(100, $user->induction_viewed_percentage);
        $this->assertSame(100, $user->inductionSummary()['accuracy']);
    }

    public function test_overdue_excludes_users_without_assignments_and_completed_users(): void
    {
        $user = $this->user();
        $user->forceFill(['created_at' => now()->subDays(8)])->save();
        $this->assertFalse((bool) $user->inductionSummary()['overdue']);
        $page = $this->page();
        $this->assertTrue((bool) $user->inductionSummary()['overdue']);
        $user->inductionViewed()->attach($page->id);
        $this->assertFalse((bool) $user->inductionSummary()['overdue']);
    }

    public function test_all_previous_modules_are_required_on_both_routes(): void
    {
        $user = $this->user();
        $first = $this->page();
        $second = $this->page(['order' => 2]);
        $third = $this->page(['order' => 3]);
        $user->inductionViewed()->attach($second->id);
        $this->actingAs($user)->get('/induction/'.$third->slug)->assertRedirect('/induction/'.$first->slug);
        $this->get('/pages/'.$third->slug)->assertRedirect('/induction/'.$first->slug);
        $this->get('/induction/completed')->assertRedirect('/lobby');
    }

    public function test_answer_saves_score_and_leaves_open_answers_ungraded(): void
    {
        $user = $this->user();
        $page = $this->page();
        $choice = $this->question($page);
        $open = $this->question($page, ['question_type' => 'abierta', 'options' => null, 'correct_option' => null]);
        $this->actingAs($user)->post('/questions/answer', ['page_id' => $page->id,
            'answers' => [$choice->id => 'Sí', $open->id => 'Explicación']])->assertSessionHasNoErrors();
        $this->assertSame(100, $user->inductionSummary()['accuracy']);
        $this->assertSame(1, $user->inductionSummary()['scored']);
        $this->assertNull(QuestionUserResponse::where('question_id', $open->id)->first()->is_correct);
    }

    public function test_incomplete_foreign_and_invalid_answers_are_rejected_without_saving(): void
    {
        $user = $this->user();
        $page = $this->page();
        $choice = $this->question($page);
        $other = $this->question($page);
        $foreign = $this->question($this->page(['destinado_a' => ['Estadía']]));
        $this->actingAs($user)->post('/questions/answer', ['page_id' => $page->id, 'answers' => [$choice->id => 'Sí']])->assertSessionHasErrors('answers');
        $this->post('/questions/answer', ['page_id' => $page->id, 'answers' => [$foreign->id => 'Sí']])->assertSessionHasErrors('answers');
        $this->post('/questions/answer', ['page_id' => $page->id, 'answers' => [$choice->id => 'Inventado', $other->id => 'Sí']])->assertSessionHasErrors('answers');
        $this->assertSame(0, QuestionUserResponse::count());
    }

    public function test_content_notice_targets_only_active_assigned_participants(): void
    {
        Mail::fake();
        $admin = $this->user(['role' => 'admin']);
        $assigned = $this->user();
        $this->user(['tipo_usuario' => 'Estadía']);
        $deleted = $this->user();
        $deleted->delete();
        $page = $this->page(['destinado_a' => ['Capital Humano']]);
        $this->actingAs($admin)->post('/admin/notify-content/'.$page->id)->assertSessionHas('success');
        Mail::assertSent(InductionContentMail::class, fn($mail) => $mail->hasTo($assigned->email));
        Mail::assertSent(InductionContentMail::class, 1);
    }

    public function test_reminder_only_for_pending_inductions(): void
    {
        Mail::fake();
        $admin = $this->user(['role' => 'admin']);
        $user = $this->user();
        $page = $this->page();
        $this->actingAs($admin)->post('/admin/send-reminder/'.$user->id)->assertSessionHas('success');
        Mail::assertSent(InductionReminderMail::class, 1);
        $user->inductionViewed()->attach($page->id);
        $this->post('/admin/send-reminder/'.$user->id)->assertSessionHas('error');
        Mail::assertSent(InductionReminderMail::class, 1);
    }

    public function test_invalid_correct_option_is_rejected(): void
    {
        $admin = $this->user(['role' => 'admin']);
        $page = $this->page();
        $this->actingAs($admin)->post('/questions', ['page_id' => $page->id, 'questions' => [[
            'question_text' => 'Prueba', 'question_type' => 'opcion_multiple', 'options' => ['Sí', 'No'], 'correct_option' => 9,
        ]]])->assertSessionHasErrors('options');
        $this->assertSame(0, Question::count());
    }

    public function test_local_backup_refuses_other_environments(): void
    {
        $this->artisan('induccion:respaldo')->assertExitCode(1);
    }

    public function test_completed_module_render_and_progress_agree(): void
    {
        $user = $this->user();
        $page = $this->page();
        $question = $this->question($page);
        $this->actingAs($user)->get('/induction/'.$page->slug)->assertOk();
        $this->assertSame(0, $user->induction_viewed_percentage);
        $this->post('/questions/answer', ['page_id' => $page->id, 'answers' => [$question->id => 'Sí']])->assertSessionHasNoErrors();
        $this->get('/pages/'.$page->slug)->assertOk()->assertSee('Aciertos: 100%');
        $this->assertSame(100, $user->induction_viewed_percentage);
    }

    public function test_partial_existing_answers_remain_in_the_form(): void
    {
        $user = $this->user();
        $page = $this->page();
        $first = $this->question($page);
        $second = $this->question($page);
        QuestionUserResponse::create(['user_id' => $user->id, 'question_id' => $first->id,
            'answer' => 'Sí', 'is_correct' => 1]);

        $this->actingAs($user)->get('/induction/'.$page->slug)->assertOk()
            ->assertSee('name="answers['.$first->id.']" value="Sí"', false);
        $this->post('/questions/answer', ['page_id' => $page->id,
            'answers' => [$first->id => 'Sí', $second->id => 'No']])->assertSessionHasNoErrors();
        $this->assertSame(100, $user->inductionSummary()['percentage']);
    }
}
