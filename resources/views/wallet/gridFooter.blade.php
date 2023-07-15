<script src="{{ asset('js/app.js') }}"></script>
<script>

    var SHOW_PLAIN_PRIVATE_KEY = {{ $SHOW_PLAIN_PRIVATE_KEY }};
    var tableId = '{{ $tableId }}';
    var networks = {!!  $networks !!};
    var UID = {{ Admin::user()->id }};


    $(function(){
        //检查ENCRYPTION_KEY 是否设置。
        const ENCRYPTION_KEY = CryptUtils.getEncryptionKey(UID)
        console.log(SHOW_PLAIN_PRIVATE_KEY)
        console.log(ENCRYPTION_KEY)

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
        $('.private-key-grid-column-qrcode,.address-grid-column-qrcode').popover({
            html: true,
            container: 'body',
            trigger: 'focus',
            content:  function () {
                // 获取后面的<span>标签
                const isAddress = $(this).hasClass('address-grid-column-qrcode');
                let content = '';
                if (isAddress) {
                    content =$(this).nextAll('div').html();
                } else {
                    content = SHOW_PLAIN_PRIVATE_KEY? $(this).nextAll('div').data('decrypted'):$(this).nextAll('div').html();
                }
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

    async function getBalance(walletAddress, rpcUrl, chainId) {
        try {
            // 创建一个以太坊提供者，连接到指定的网络
            const provider = new ethers.providers.JsonRpcProvider(rpcUrl, { chainId });

            // 获取钱包地址对应的以太坊余额
            const balance = await provider.getBalance(walletAddress);

           // return 0;
            // 将以太坊余额转换为可读格式
           const formattedBalance = parseFloat(ethers.utils.formatEther(balance)).toFixed(4);

           return formattedBalance;
        } catch (error) {
            console.error('Error fetching balance:', error);
            return null;
        }
    }


    $('.column-address').find('div').each(async function(){
        let address = $(this).html().trim();
        let t = this;

        networks.forEach(function(network ){
            getBalance(address, network.rpc_url, network.chain_id)
                .then((balance) => {
                    console.log(network.name + ' Balance '+balance)
                   // console.log($(t).parent().siblings('column-network-'+id+'-balance'))
                    $(t).parent().siblings('.column-network-'+network.id+'-balance').html(balance );
                })
                .catch((error) => {
                    console.error('Error:', error);
                });
        });



    });


</script>
