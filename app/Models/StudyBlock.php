<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudyBlock extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'goal_id', 'schedule_id', 'content', 'date', 'completed'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function goal()
    {
        return $this->belongsTo(Goal::class);
    }

    public function schedule() {
        return $this->belongsTo(Schedule::class);
    }
}
