<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'video_url',
        'video_file',
        'attachment',
        'order',
        'destinado_a',
    ];

    protected $casts = [
        'destinado_a' => 'array',
    ];

    public function getEsColaboradorAttribute()
    {
        $destinos = is_array($this->destinado_a) ? $this->destinado_a : [$this->destinado_a];
        $destinosNorm = array_map(fn($d) => mb_strtolower(trim((string)$d)), $destinos);
        return !in_array('estadia', $destinosNorm) && !in_array('ambos', $destinosNorm);
    }

    public function questions()
    {
        return $this->hasMany(Question::class, 'page_id');
    }

    protected static function boot()
    {
        parent::boot();
        static::deleting(function ($page) {
            $page->questions()->delete();
        });
    }
}