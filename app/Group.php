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
    //
    public function wallets()
    {
        return $this->hasMany(Wallet::class);
    }

    /**
     * @throws \Exception
     */
    public function generateWallets($walletCount, $coinType=60)
    {
        if ($walletCount > 0 && !empty($this->mnemonic)) {

            // Generate a seed from mnemonic/password
            $seedGenerator = new Bip39SeedGenerator();

            $seed = Helper::decryptString($this->mnemonic,Helper::padKey(env('ENCRYPTION_KEY')));
          //  $seed = 'process minute awesome throw always lounge alone almost spice door october cattle';
            $seed = $seedGenerator->getSeed($seed, '');

            // Derive the master key from the seed
            $hdFactory = new HierarchicalKeyFactory();
            $master = $hdFactory->fromEntropy($seed);

            // Generate wallets using bitwasp/bitcoin library
            $walletsData = [];

            $util = new Util();
            for ($i = 0; $i < $walletCount; $i++) {

                $derivePath =   "44'/{$coinType}'/0'/0/{$i}";
                $child = $master->derivePath($derivePath);
                $privateKey = $child->getPrivateKey();
                $publicKey = $child->getPublicKey();

                $finalPrivateKey = '';
                $address ='';
                if ($coinType != 0){
                    $address = $util->publicKeyToAddress($util->privateKeyToPublicKey($privateKey->getHex()));
                    $finalPrivateKey = $privateKey->getHex();
                } else {
                    $pubKeyHash = $publicKey->getPubKeyHash();
                    $p2pkh = new PayToPubKeyHashAddress($pubKeyHash);
                    $address = $p2pkh->getAddress();
                    $finalPrivateKey =  $privateKey->toWif();
                }

                $finalPrivateKey = Helper::encryptString($finalPrivateKey, Helper::padKey(env('ENCRYPTION_KEY')));
                // Build wallet data
                $walletData = [
                    'private_key' => $finalPrivateKey,
                    'address' => $address,
                    'path'=> $derivePath,
                    // Other fields
                ];

                $walletsData[] = $walletData;
            }

         //    Store generated wallets in the wallets table
            foreach ($walletsData as $walletData) {
                $wallet = Wallet::updateOrCreate(
                    ['address' => $walletData['address']],
                    [
                        'group_id' => $this->id,
                        'private_key' => $walletData['private_key'] ,
                        'address' => $walletData['address'],
                        'path'=> $walletData['path']
                        // Other fields
                    ]
                );
            }
        }
    }
}
