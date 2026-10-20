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

    /**
     * Calcula dinámicamente el progreso porcentual basándose en su puesto de trabajo real
     */
    public function getInductionViewedPercentageAttribute()
    {
        $allPages = Page::all();
        
        $puestoUser = str_replace(['í', 'á', 'é', 'ó', 'ú'], ['i', 'a', 'e', 'o', 'u'], mb_strtolower(trim($this->puesto ?? '')));
        $areaUser = str_replace(['í', 'á', 'é', 'ó', 'ú'], ['i', 'a', 'e', 'o', 'u'], mb_strtolower(trim($this->area ?? '')));
        $tipoUser = str_replace(['í', 'á', 'é', 'ó', 'ú'], ['i', 'a', 'e', 'o', 'u'], mb_strtolower(trim($this->tipo_usuario ?? '')));
        
        $esEstadia = str_contains(strtolower($this->role ?? ''), 'estadia') ||
                     str_contains(strtolower($this->tipo_usuario ?? ''), 'estadia') ||
                     str_contains(strtolower($this->puesto ?? ''), 'estadia');

        $assignedPages = $allPages->filter(function($page) use ($puestoUser, $areaUser, $tipoUser, $esEstadia) {
            $destinadoRaw = $page->destinado_a;
            $listaPuestos = is_array($destinadoRaw) ? $destinadoRaw : (is_string($destinadoRaw) ? explode(',', $destinadoRaw) : (array)$destinadoRaw);
            
            $puestosNormalizados = array_map(function($item) {
                $str = mb_strtolower(trim((string)$item));
                return str_replace(['í', 'á', 'é', 'ó', 'ú'], ['i', 'a', 'e', 'o', 'u'], $str);
            }, $listaPuestos);

            if (array_intersect(['ambos', 'todos', 'general'], $puestosNormalizados)) {
                return true;
            }

            if ($esEstadia) {
                return in_array('estadia', $puestosNormalizados);
            }

            return in_array('colaborador', $puestosNormalizados) ||
                   ($puestoUser && in_array($puestoUser, $puestosNormalizados)) ||
                   ($areaUser && in_array($areaUser, $puestosNormalizados)) ||
                   ($tipoUser && in_array($tipoUser, $puestosNormalizados));
        });

        $totalSteps = $assignedPages->count();
        if ($totalSteps <= 0) return 0;

        $completedSteps = $this->inductionViewed()->whereIn('page_id', $assignedPages->pluck('id'))->distinct()->count();
        $percentage = round(($completedSteps / $totalSteps) * 100);

        return min($percentage, 100);
    }

    public function viewedPages()
    {
        return $this->belongsToMany(Page::class, 'user_page_views')->withTimestamps();
    }
}