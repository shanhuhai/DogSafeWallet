function getEncryptionKey() {
    var encryptionKey = localStorage.getItem('ENCRYPTION_KEY');

    if (encryptionKey && encryptionKey.trim() !== '') {
        return encryptionKey;
    } else {
        encryptionKey = prompt('请输入加密密钥, 服务器端不会保存此秘钥， 请自行保管好');

        if (encryptionKey.length >= 6 && encryptionKey.length <= 32) {
            localStorage.setItem('ENCRYPTION_KEY', encryptionKey);
            return encryptionKey;
        } else {
            alert('加密密钥长度不符合要求，请重新输入');
            return getEncryptionKey(); // 重新要求填写加密密钥
        }
    }
}

function maskString(string, starNum = 3, left = 6, right = 4) {
    const length = string.length;
    if (left === 0 && right === 0) {
        return '*'.repeat(starNum);
    }
    return string.substring(0, left) + '*'.repeat(starNum) + string.substring(length - right);
}


function arrayBufferToBase64(buffer) {
    var binary = '';
    var bytes = new Uint8Array(buffer);
    for (var i = 0; i < bytes.byteLength; i++) {
        binary += String.fromCharCode(bytes[i]);
    }
    return btoa(binary);
}
function padKey(key) {
    while (key.length < 32) {
        key += '0';
    }
    return key;
}

function base64ToArrayBuffer(base64) {
    var binary = atob(base64);
    var length = binary.length;
    var buffer = new ArrayBuffer(length);
    var view = new Uint8Array(buffer);
    for (var i = 0; i < length; i++) {
        view[i] = binary.charCodeAt(i);
    }
    return buffer;
}

function splitArrayBuffer(arrayBuffer, ivLength) {
    var iv = arrayBuffer.slice(0, ivLength);
    var data = arrayBuffer.slice(ivLength);
    return { iv: iv, data: data };
}

function splitString(message, blockSize) {
    var result = {};
    result.iv = new Uint8Array(blockSize);
    crypto.getRandomValues(result.iv);
    result.data = new Uint8Array(blockSize * Math.ceil(message.length / blockSize));
    for (var i = 0; i < message.length; i++) {
        result.data[i] = message.charCodeAt(i);
    }
    return result;
}

async function encryptString(messageString, keyString){

    async function openssl_encrypt(message, key, iv) {
        // 密钥导入
        var importedKey = await crypto.subtle.importKey(
            "raw",
            key,
            "AES-CBC",
            false,
            ["encrypt"]
        );

        // 加密
        var encodedMessage = new TextEncoder().encode(message);
        var encrypted = await crypto.subtle.encrypt(
            {
                name: "AES-CBC",
                iv: iv
            },
            importedKey,
            encodedMessage
        );

        return encrypted;
    }

    function arrayBufferToBase64(buffer) {
        var binary = '';
        var bytes = new Uint8Array(buffer);
        for (var i = 0; i < bytes.byteLength; i++) {
            binary += String.fromCharCode(bytes[i]);
        }
        return btoa(binary);
    }

    var encoder = new TextEncoder();
    keyString = padKey(keyString);
    var keyData = encoder.encode(keyString);
    var key = new Uint8Array(keyData);

    // 生成随机的初始化向量 (IV)
    var iv = new Uint8Array(16);
    crypto.getRandomValues(iv);

    var encryptedBuffer = await openssl_encrypt(messageString, key, iv);

    // 将加密结果与 IV 拼接
    var combinedBuffer = new Uint8Array(iv.byteLength + encryptedBuffer.byteLength);
    combinedBuffer.set(iv);
    combinedBuffer.set(new Uint8Array(encryptedBuffer), iv.byteLength);
    var encryptedString = arrayBufferToBase64(combinedBuffer);
    return encryptedString;

}
async function decryptString(encryptedString, keyString) {
    async function openssl_decrypt(encrypted, key, iv) {
        // 密钥导入
        var importedKey = await crypto.subtle.importKey(
            "raw",
            key,
            "AES-CBC",
            false,
            ["decrypt"]
        );

        // 解密
        var decrypted = await crypto.subtle.decrypt(
            {
                name: "AES-CBC",
                iv: iv
            },
            importedKey,
            encrypted
        );

        // 将解密结果转换为字符串
        var decryptedString = new TextDecoder().decode(decrypted);
        return decryptedString;
    }

    var encoder = new TextEncoder();
    keyString = padKey(keyString);
    var keyData = encoder.encode(keyString);

    var key = new Uint8Array(keyData);

    var arrayBuffer = base64ToArrayBuffer(encryptedString);
    var result = splitArrayBuffer(arrayBuffer, 16);
    var ivBuffer = result.iv;
    var encryptedBuffer = result.data;

    return await openssl_decrypt(encryptedBuffer, key, ivBuffer);
}

var CryptUtils = {
    encryptString: encryptString,
    decryptString: decryptString,
    getEncryptionKey: getEncryptionKey,
    maskString: maskString
};

module.exports = CryptUtils;
