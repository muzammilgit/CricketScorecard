<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class comment extends Model
{
    //
    public function posts()
    {
        return $this->belongsTo('App\Comment');
    }
}
