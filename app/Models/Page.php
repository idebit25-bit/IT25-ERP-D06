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

    // También admite el texto simple que guardaban las versiones anteriores.
    public function getDestinadoAAttribute($value)
    {
        if (is_array($value)) return $value;
        $decoded = json_decode((string) $value, true);
        if (is_array($decoded)) return $decoded;
        return array_filter(explode(',', is_string($decoded) ? $decoded : (string) $value));
    }

    public function accessibleBy(User $user): bool
    {
        if ($user->role === 'admin') return true;
        $clean = fn($value) => str_replace(
            ['í', 'á', 'é', 'ó', 'ú', 'ñ'], ['i', 'a', 'e', 'o', 'u', 'n'],
            mb_strtolower(trim((string) $value), 'UTF-8')
        );
        $destinations = array_map($clean, $this->destinado_a);
        if (array_intersect(['ambos', 'todos', 'general'], $destinations)) return true;
        $profiles = array_map($clean, [$user->tipo_usuario, $user->puesto, $user->area]);
        $isIntern = str_contains($clean($user->role), 'estadia')
            || str_contains($profiles[0], 'estadia') || str_contains($profiles[1], 'estadia');
        if ($isIntern) return in_array('estadia', $destinations, true);
        return in_array('colaborador', $destinations, true)
            || (bool) array_intersect(array_filter($profiles), $destinations);
    }

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
