<script src="{{ asset('js/app.js') }}"></script>
<script>
    const fetchBalance = async (address) => {
        try {
            const response = await fetch(`https://mainnet.infura.io/v3/{{ env('INFURA_API_KEY') }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    jsonrpc: '2.0',
                    id: 1,
                    method: 'eth_getBalance',
                    params: [address, 'latest'],
                }),
            });

            const data = await response.json();
            // 处理返回的数据，将十六进制的余额转为十进制
            const balance = parseInt(data.result, 16);

            const balanceWei = parseInt(data.result, 16);
            const balanceEth = balanceWei / 1e18; // 转换为 ETH 单位

            // 保留 8 位小数
            const balanceEthFormatted = balanceEth.toFixed(4);
            return balanceEthFormatted;
        } catch (error) {
            console.error('Error fetching balance:', error);
            throw error;
        }
    };

    const provider = new zksync.Provider("https://mainnet.era.zksync.io");
    $('.column-address').find('span.hidden-address').each(async function(){
        let address = $(this).html().trim();

        const balance = await fetchBalance(address);
        $(this).parent().siblings('.column-balance').html(balance);
         const zksBalance = await provider.getBalance(address) ;
        $(this).parent().siblings('.column-zksBalance').html(ethers.utils.formatEther(zksBalance));

     });


</script>
