<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AdminUserNetwork extends Model
{
    //
    public $timestamps = false;
    protected $fillable = ['user_id', 'network_id'];
}
