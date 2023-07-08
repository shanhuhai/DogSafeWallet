<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use function App\Admin\Controllers\maskString;

class Wallet extends Model
{

   // protected $appends = ['decrypted_private_key'];

    protected $fillable = ['user_id','group_id', 'mnemonic_id', 'wallet', 'encrypted_private_key', 'address', 'path'];
    //
    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function mnemonic()
    {
        return $this->belongsTo(Group::class);
    }

//    public function getDecryptedPrivateKeyAttribute()
//    {
//        return Helper::decryptString($this->encrypted_private_key, Helper::padKey(env('ENCRYPTION_KEY')));;
//    }

}
