<template>
    <div>
        <button @click="generateMnemonic">生成助记词</button>
        <div>{{ mnemonic }}</div>
        <button @click="generateAddresses">生成地址</button>
        <ul>
            <li v-for="(address, index) in addresses" :key="index">
                <strong>{{ index + 1 }}.</strong> 私钥：{{ address.privateKey }}<br>
                地址：{{ address.address }}<br><br>
            </li>
        </ul>
        <example-component></example-component>
    </div>
</template>

<script>
import bip39 from 'bip39';
import bitcoin from 'bitcoinjs-lib';
import $ from 'jquery'

export default {
    data() {
        return {
            mnemonic: '',
            addresses: []
        };
    },
    mounted() {
        bip39.generateMnemonic();
        $('#app').css({'color':'red'})
    },
    methods: {
        generateMnemonic() {
            // 生成助记词
            console.log(bip39)
            this.mnemonic = bip39.generateMnemonic();
        },
        generateAddresses() {
            // 生成私钥和钱包地址对
            const mnemonicToSeed = bip39.mnemonicToSeedSync(this.mnemonic);
            const root = bitcoin.bip32.fromSeed(mnemonicToSeed);
            const addresses = [];

            for (let i = 0; i < 10; i++) {
                const child = root.derivePath(`m/44'/0'/0'/0/${i}`);
                const privateKey = child.toWIF();
                const address = bitcoin.payments.p2pkh({ pubkey: child.publicKey }).address;
                addresses.push({ privateKey, address });
            }

            this.addresses = addresses;
        }
    }
};
</script>
