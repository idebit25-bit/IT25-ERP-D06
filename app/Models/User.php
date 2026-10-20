<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Page;
use App\Models\Question;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',          // Importante para admin/user
        'tipo_usuario',  // Importante para distinguir entre Colaborador y Estadía
        'area',          // Guarda el área/puesto específico del colaborador
        'puesto',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'deleted_at' => 'datetime',
    ];

    public function pagesViewed()
    {
        return $this->belongsToMany(Page::class, 'page_user');
    }

    public function pagesViewedCount()
    {
        return $this->pagesViewed()->count();
    }

    public function inductionViewed()
    {
        return $this->belongsToMany(Page::class, 'induction_page_user');
    }

    public function inductionViewedCount()
    {
        return $this->inductionViewed()->count();
    }

    public function inductionViewedQuestions()
    {
        return $this->belongsToMany(Question::class, 'induction_question_user');
    }

    public function responses()
    {
        return $this->hasMany(QuestionUserResponse::class);
    }

    public function inductionSummary($pages = null): array
    {
        $pages ??= Page::with('questions')->orderBy('order')->orderBy('id')->get();
        $assigned = $pages->filter(fn($page) => $page->accessibleBy($this));
        $viewed = $this->relationLoaded('inductionViewed')
            ? $this->inductionViewed->pluck('id')->all()
            : $this->inductionViewed()->pluck('pages.id')->all();
        $responses = $this->relationLoaded('responses') ? $this->responses : $this->responses()->get();
        $answered = $responses->filter(fn($r) => trim((string) $r->answer) !== '')->pluck('question_id')->unique()->all();
        $completedPages = $assigned->filter(function ($page) use ($viewed, $answered) {
            $activeIds = $page->questions->where('is_active', 1)->pluck('id')->all();
            return $activeIds ? !array_diff($activeIds, $answered) : in_array($page->id, $viewed);
        });
        $completed = $completedPages->count();
        $activeQuestionIds = $assigned->flatMap(fn($page) => $page->questions->where('is_active', 1)->where('question_type', 'opcion_multiple')->pluck('id'))->all();
        $scored = $responses->whereIn('question_id', $activeQuestionIds)->filter(fn($r) => $r->is_correct !== null);
        $correct = $scored->filter(fn($r) => (bool) $r->is_correct)->count();
        $total = $assigned->count();
        $percentage = $total ? (int) round($completed / $total * 100) : 0;
        if ($total > $completed) $percentage = min(99, $percentage);
        $days = max(1, (int) config('induction.delay_days', 7));
        return [
            'total' => $total, 'completed' => $completed, 'percentage' => $percentage,
            'completed_ids' => $completedPages->pluck('id')->all(),
            'scored' => $scored->count(), 'correct' => $correct,
            'accuracy' => $scored->count() ? (int) round($correct / $scored->count() * 100) : null,
            'overdue' => $this->role === 'user' && !$this->deleted_at && $total > $completed
                && $this->created_at && $this->created_at->lte(now()->subDays($days)),
        ];
    }

    public function getInductionViewedPercentageAttribute()
    {
        return $this->inductionSummary()['percentage'];
    }
    public function viewedPages()
    {
        return $this->belongsToMany(Page::class, 'user_page_views')->withTimestamps();
    }
}
