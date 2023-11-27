<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Goal extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'type', 'test_date', 'content_to_study'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function studyBlocks()
    {
        return $this->hasMany(StudyBlock::class);
    }
}
