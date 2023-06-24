const bip39 = require('bip39');
const ethUtil = require('ethereumjs-util');
const bitcoin = require('bitcoinjs-lib');
const ethers = require('ethers');


class Wallet {
    static generateMnemonic() {
        return bip39.generateMnemonic(128);
    }

    static generateWalletsFromMnemonic(mnemonic, coinCode, numWallets, offset=0) {
        const wallets = [];
        const seed = bip39.mnemonicToSeedSync(mnemonic);
        console.log(offset)

        for (let i = offset; i < numWallets+offset; i++) {
            let privateKey, address, derivePath;

            if (coinCode === 0) {
                // Generate Bitcoin wallet
                const bitcoinNetwork = bitcoin.networks.bitcoin;
                const bitcoinNode = bitcoin.bip32.fromSeed(seed, bitcoinNetwork);
                derivePath = `m/44'/0'/0'/0/${i}`
                const bitcoinChild = bitcoinNode.derivePath(derivePath);
                privateKey = bitcoinChild.privateKey.toString('hex');
                address = bitcoin.payments.p2pkh({ pubkey: bitcoinChild.publicKey, network: bitcoinNetwork }).address;
            } else if (coinCode === 60) {
                // Generate Ethereum wallet
                derivePath = `m/44'/60'/0'/0/${i}`
                const ethereumNode = ethers.Wallet.fromMnemonic(mnemonic, `m/44'/60'/0'/0/${i}`);
                privateKey = ethereumNode.privateKey;
                address = ethereumNode.address;
            } else {
                throw new Error('Unsupported coin code');
            }

            wallets.push({
                privateKey,
                address,
                derivePath
            });
        }

        return wallets;
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
