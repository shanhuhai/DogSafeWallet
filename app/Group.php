<?php

namespace App;

use BitWasp\Bitcoin\Address\PayToPubKeyHashAddress;
use Illuminate\Database\Eloquent\Model;
use BitWasp\Bitcoin\Bitcoin;
use BitWasp\Bitcoin\Key\Factory\HierarchicalKeyFactory;

use BitWasp\Bitcoin\Mnemonic\Bip39\Bip39SeedGenerator;

use Web3p\EthereumUtil\Util;


class Group extends Model
{
    protected $fillable = ['user_id', 'name'];
    //
    public function wallets()
    {
        return $this->hasMany(Wallet::class);
    }

}
