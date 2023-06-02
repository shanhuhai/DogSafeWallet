<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use function App\Admin\Controllers\maskString;

class Wallet extends Model
{

    protected $appends = ['decrypted_private_key'];

    protected $fillable = ['group_id', 'wallet', 'private_key', 'address', 'path'];
    //
    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function getDecryptedPrivateKeyAttribute()
    {
        return Helper::decryptString($this->private_key, Helper::padKey(env('ENCRYPTION_KEY')));;
    }

}
