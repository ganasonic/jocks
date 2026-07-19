<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TrainingDetail extends Model
{
    protected $fillable = ['training_id', 'exercise_name', 'weight', 'reps', 'sets', 'interval', 'rpe'];
}
