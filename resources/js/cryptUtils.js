function getEncryptionKey(UID) {
    var encryptionKey = localStorage.getItem(UID+'ENCRYPTION_KEY');

    if (encryptionKey && encryptionKey.trim() !== '') {
        return encryptionKey;
    } else {
        return Swal.fire({
            title: '请输入加密密钥',
            html: `
        <div class="input-group">
          <input type="password" id="encryption-key-input" class="form-control" placeholder="加密密钥">
          <div class="input-group-addon">
            <i id="toggle-password" class="fa fa-eye-slash" style="cursor: pointer"></i>
          </div>

        </div>
        <div class="swal2-help-icon">
        <i id="help-icon" class="fa fa-question-circle" style="color: red"></i>
        </div>
        <div class="checkbox">
<!--        <label for="store-key-checkbox" >-->
<!--          <input type="checkbox" style="margin-top:2px" id="store-key-checkbox"> 托管到服务器-->
<!--        </label>-->
        </div>
      `,
            focusConfirm: false,
            showCancelButton: false,
            confirmButtonText: '确认',
            showLoaderOnConfirm: true,
            preConfirm: () => {
                var key = $('#encryption-key-input').val();
                var storeKeyOnServer = $('#store-key-checkbox').prop('checked');
                return new Promise((resolve, reject) => {
                    if (key.length >= 6 && key.length <= 32) {
                        if (storeKeyOnServer) {
                            saveKeyOnServer(key)
                                .then(() => {
                                    localStorage.setItem(UID+'ENCRYPTION_KEY', key);
                                    resolve(key);
                                })
                                .catch((error) => {
                                    reject(`无法将密钥保存到服务器: ${error}`);
                                });
                        } else {
                            localStorage.setItem(UID+'ENCRYPTION_KEY', key);
                            resolve(key);
                        }
                    } else {
                        reject('加密密钥长度不符合要求，请重新输入');
                    }
                });
            },
            allowOutsideClick: false,
            onBeforeOpen: () => {

                $('.swal2-help-icon').appendTo('.swal2-title');
                $('#help-icon').popover({
                    content: "<div><h4>注意：</h4>1.加密密钥用于加密您的私钥和助记词等敏感数据;<br>" +
                        "2.加解密密过程完全在前端进行，后端不会存储您的明文私钥和助记词;<br>" +
                        "3.后端也不会存储您的加密密钥，请自行备份好。<br>" +
                        "4.加密密钥存储在您的浏览器缓存中， 没有过期时间，但是一旦浏览器缓存被清除，或换了新设备， 则需要您重新输入。<br>" +
                        "5.加密密钥只能设置一次，后续不可修改， 一旦修改意味着修改前的数据将无法被解密。<br>"+
                        "6.因为后端只存储了加密数据，且解密秘钥只存储在您的浏览上， 即便网站被攻克，也能保证不会泄露您的私钥和助记词</div>",
                    trigger: 'hover',
                    placement: 'right',
                    container: 'body',
                    html: true
                });
            }
        }).then((result) => {
            if (result.isConfirmed) {
                window.reload()
                return result.value;
            } else {
                return getEncryptionKey(UID); // 重新要求填写加密密钥
            }
        }).catch((error) => {
            Swal.showValidationMessage(error);
            return getEncryptionKey(UID); // 重新要求填写加密密钥
        });
    }
}
$('body').on('click', '#toggle-password',function() {
    var encryptionKeyInput = $('#encryption-key-input');
    var togglePassword = $(this);

    if (encryptionKeyInput.attr('type') === 'password') {
        encryptionKeyInput.attr('type', 'text');
        togglePassword.removeClass('fa-eye-slash').addClass('fa-eye');
    } else {
        encryptionKeyInput.attr('type', 'password');
        togglePassword.removeClass('fa-eye').addClass('fa-eye-slash');
    }
});

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
