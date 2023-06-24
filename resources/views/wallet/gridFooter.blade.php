<script src="{{ asset('js/app.js') }}"></script>
<script>

    var SHOW_PLAIN_PRIVATE_KEY = {{ \App\Helper::config('show_plain_private_key') }};
    var tableId = '{{ $tableId }}';


    $(function(){
        //检查ENCRYPTION_KEY 是否设置。
        const ENCRYPTION_KEY = CryptUtils.getEncryptionKey()
        console.log(SHOW_PLAIN_PRIVATE_KEY)
        console.log(ENCRYPTION_KEY)
        //如果设置了显示明文秘钥, 则解密
        if(SHOW_PLAIN_PRIVATE_KEY) {
            $('.column-encrypted_private_key div').each(function() {
                CryptUtils.decryptString($(this).text().trim(), ENCRYPTION_KEY)
                    .then(privateKey => {
                        console.log($(this).text());
                        console.log(privateKey);
                        $(this).data('decrypted',privateKey);
                        $(this).parent().find('span').text(CryptUtils.maskString(privateKey));

                    })
                    .catch(error => {
                        console.error(error);
                    });
            });
        }
        //处理复制
        $('#'+tableId).on('click','.private-key-grid-column-copyable',(async function (e) {
            var content = $(this).data('content');
            //如果显示明文私钥则需要先解密
            content = SHOW_PLAIN_PRIVATE_KEY ? await CryptUtils.decryptString(content,ENCRYPTION_KEY) : content;

            var temp = $('<input>');

            $("body").append(temp);
            temp.val(content).select();
            document.execCommand("copy");
            temp.remove();

            $(this).tooltip('show');
        }));
        //处理二维码
        $('.private-key-grid-column-qrcode').popover({
            html: true,
            container: 'body',
            trigger: 'focus',
            content:  function () {
                // 获取后面的<span>标签
                let content = SHOW_PLAIN_PRIVATE_KEY? $(this).nextAll('div').data('decrypted'):$(this).nextAll('div').html();
                qrcodeNode = $('<div>').css({ width:150, height:150});
                var qrcode = new QRCode(qrcodeNode[0], {
                    colorDark : "#000000",
                    colorLight : "#ffffff",
                    text: content,
                    correctLevel : QRCode.CorrectLevel.H,
                    useSVG: true
                });

                 return qrcodeNode[0].outerHTML;
            }
        });


    })

  //  https://mainnet.infura.io/v3/INFURA_API_KEY

    const fetchBalance = async (address) => {
        try {
            const provider = new ethers.providers.JsonRpcProvider('https://cloudflare-eth.com');

            const balanceWei = await provider.getBalance(address);
            const balanceEth = ethers.utils.formatEther(balanceWei);

            // Keep 8 decimal places
            const balanceEthFormatted = parseFloat(balanceEth).toFixed(4);

            return balanceEthFormatted;
        } catch (error) {
            console.error('Error fetching balance:', error);
            throw error;
        }
    }

    const provider = new zksync.Provider("https://mainnet.era.zksync.io");
    $('.column-address').find('span.hidden-address').each(async function(){
        let address = $(this).html().trim();

        const balance = await fetchBalance(address);
        $(this).parent().siblings('.column-balance').html(balance);
         let zksBalance = await provider.getBalance(address) ;

        zksBalance = ethers.utils.formatEther(zksBalance);
        zksBalance = parseFloat(zksBalance).toFixed(4);
        $(this).parent().siblings('.column-zksBalance').html(zksBalance);

     });


</script>
