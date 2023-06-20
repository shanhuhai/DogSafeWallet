const bip39 = require('bip39');
const ethUtil = require('ethereumjs-util');
const bitcoin = require('bitcoinjs-lib');


class Wallet {
    static generateMnemonic() {
        return bip39.generateMnemonic(128);
    }

    // static getAddressFromPrivateKey(privateKey) {
    //     const keyPair = bitcoin.ECPair.fromPrivateKey(Buffer.from(privateKey, 'hex'));
    //     const { address } = bitcoin.payments.p2pkh({ pubkey: keyPair.publicKey });
    //     return address;
    // }
    static generatePrivateKeyFromMnemonic(mnemonic, index) {
        const seed = bip39.mnemonicToSeedSync(mnemonic);
        const hdWallet = hdkey.fromMasterSeed(seed);
        const path = `m/44'/60'/0'/0/${index}`;
        const wallet = hdWallet.derivePath(path).getWallet();
        const privateKey = wallet.getPrivateKeyString();
        return privateKey;
    }

    static getAddressFromPrivateKey(privateKey) {
        if (!privateKey.startsWith('0x')) {
            privateKey = '0x' + privateKey;
        }
        const privateKeyBuffer = ethUtil.toBuffer(privateKey);
        const wallet = ethUtil.privateToPublic(privateKeyBuffer);
        const address = ethUtil.publicToAddress(wallet).toString('hex');
        return '0x'+address;
    }
}

module.exports = Wallet;
