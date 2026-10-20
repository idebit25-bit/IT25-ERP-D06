<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Step extends Model {
    use HasFactory;

    protected $fillable = ['title', 'description', 'content_type', 'content_url', 'order', 'dependency_id'];

    public function dependency() {
        return $this->belongsTo(Step::class, 'dependency_id');
    }
}
