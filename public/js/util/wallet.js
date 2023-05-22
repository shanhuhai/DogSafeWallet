const bip39 = require('bip39');
const hdkey = require('ethereumjs-wallet/hdkey');
const ethUtil = require('ethereumjs-util');

class Wallet {
    static generateMnemonic() {
        return bip39.generateMnemonic();
    }

    static generatePrivateKeyFromMnemonic(mnemonic, index) {
        const seed = bip39.mnemonicToSeedSync(mnemonic);
        const hdWallet = hdkey.fromMasterSeed(seed);
        const path = `m/44'/60'/0'/0/${index}`;
        const wallet = hdWallet.derivePath(path).getWallet();
        const privateKey = wallet.getPrivateKeyString();
        return privateKey;
    }

    static generateWalletAddress(privateKey) {
        const privateKeyBuffer = ethUtil.toBuffer(privateKey);
        const wallet = ethUtil.privateToPublic(privateKeyBuffer);
        const address = ethUtil.publicToAddress(wallet).toString('hex');
        return address;
    }
}

module.exports = Wallet;
