
    $(function(){

        $('form').submit(function(event){
            event.preventDefault();
             $("#private_key").prop("disabled", true);
            $(this).submit();
        });
        $('#private_key').on('input', async function() {
            var privateKey = $(this).val();
            let address =  Wallet.getAddressFromPrivateKey(privateKey);
            $('#address').val(address); // 自动生成地址
            let ENCRYPTION_KEY = CryptUtils.getEncryptionKey()
            let encryptedPrivateKey = await CryptUtils.encryptString(privateKey, ENCRYPTION_KEY)
            $('input[name="encrypted_private_key"]').val(encryptedPrivateKey);
        });
    })

