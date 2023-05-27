<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{

    protected $fillable = ['group_id', 'wallet', 'private_key', 'address', 'path'];
    //
    public function group()
    {
        return $this->belongsTo(Group::class);
    }
}
