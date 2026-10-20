<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_text',
        'question_type',
        'options',
        'correct_option',
        'order',
        'is_active',
    ];

    public function userResponses()
    {
        return $this->hasMany(QuestionUserResponse::class, 'question_id');
    }


    public function page()
    {
        return $this->belongsTo(\App\Models\Page::class, 'page_id');
    }
}
