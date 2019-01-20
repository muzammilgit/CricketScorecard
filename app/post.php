<?php
/**
 * Created by PhpStorm.
 * User: rakshit
 * Date: 7/1/19
 * Time: 2:44 PM
 */

namespace App;
use Illuminate\Database\Eloquent\Model;

class post extends Model
{
    public function comments()
    {
        return $this->hasMany('App\comment');
    }
}