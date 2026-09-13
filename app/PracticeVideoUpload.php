<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PracticeVideoUpload extends Model
{
    protected $guarded = [];
    protected $dates = ['expires_at'];

    public function detail()
    {
        return $this->belongsTo(PracticeDetail::class, 'detail_id');
    }
}
