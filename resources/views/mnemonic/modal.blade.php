<div class="modal fade" id="generate-wallets-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Generate Wallets</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <label for="wallet-count-input">Wallet Count:</label>
                <input type="number" class="form-control" id="wallet-count-input" min="1" step="1" value="3">
                <label for="wallet-group-input">Group:</label>
                <select id="wallet-group-input" class="form-control">
                    @foreach($groups as $id=>$name):
                    <option value="{{$id}}">{{$name}}</option>
                    @endforeach
                </select>
                <div class="modal-body-wallets" style="max-height:300px;overflow:scroll">
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button id="submit-btn" type="button" class="btn btn-primary">Submit</button>
            </div>
        </div>
    </div>
</div>
<script src="{{ asset('js/app.js') }}"></script>
<script type="text/javascript">
    var UID = {{ Admin::user()->id }}
    $(function(){

        var mnemonicId;
        var mnemonic;
        var offset ={};
        var wallets = {};
        const ENCRYPTION_KEY = CryptUtils.getEncryptionKey(UID);

        function generateWallets(mnemonic, coinCode,walletNum){

            wallets =  Wallet.generateWalletsFromMnemonic(mnemonic, coinCode, walletNum,offset[mnemonic]);

            let tbody = $('#generate-wallets-modal').find('.modal-body-wallets tbody');

            if (tbody.length === 0) {
                let table = $('<table>').addClass('table');
                let thead = $('<thead>').append(
                    $('<tr>').append(
                        $('<th>').text('Derive Path'),
                        $('<th>').text('Address'),
                        $('<th>').text('Private Key')
                    )
                );
                tbody = $('<tbody>');
                table.append(thead, tbody);
                $('#generate-wallets-modal').find('.modal-body-wallets').append(table);
            }

            for (let i = 0; i < wallets.length; i++) {
                let wallet = wallets[i];
                let row = $('<tr>').append(

                    $('<td>').text(wallet.derivePath),
                    $('<td>').text(wallet.address),
                    $('<td>').text(CryptUtils.maskString(wallet.privateKey))
                );
                tbody.append(row);
            }


        }
        $('#wallet-count-input').change(function(){
            let walletCount = $(this).val()
            if (isNaN(walletCount) || walletCount <= 0) {
                alert('Invalid wallet count. Please enter a positive number.');
                return;
            }
            walletCount = parseInt(walletCount)
            if(walletCount>offset[mnemonic]){
                generateWallets(mnemonic, 60, walletCount-offset[mnemonic],offset[mnemonic]);
                offset[mnemonic] = walletCount
            }
        });
        //批量生成钱包
        $(document).on('click', '.generate-wallets-btn', async function() {
             mnemonicId = $(this).data('mnemonic-id');
             let encryptedMnemonic = $(this).data('encrypted-mnemonic');

            try {
                mnemonic = await CryptUtils.decryptString(encryptedMnemonic, ENCRYPTION_KEY);
            } catch (error) {
                console.error('解密助记词时发生错误', error);
                // 在这里可以添加自定义的错误处理逻辑
                return;
            }

            $('#generate-wallets-modal').find('.modal-body-wallets tbody').empty();
            let walletCount = 3;
            $('#wallet-count-input').val(walletCount)
            offset[mnemonic] = 0;
            generateWallets(mnemonic, 60, walletCount);
            offset[mnemonic] = walletCount;

            // 显示模态框弹窗
            $('#generate-wallets-modal').modal('show');

        });


        $('#submit-btn').on('click', async function() {
            var groupId = $('#wallet-group-input').val();

            try {
                await Promise.all(wallets.map(async function(wallet) {
                    wallet.privateKey = await CryptUtils.encryptString(wallet.privateKey, ENCRYPTION_KEY);
                }));
            } catch (error) {
                console.error('加密私钥时发生错误', error);
                // 在这里可以添加自定义的错误处理逻辑
                return;
            }
            let data = JSON.stringify({
                user_id: UID,
                mnemonic_id: mnemonicId,
                group_id:groupId,
                wallets: wallets
            })

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{{csrf_token()}}'
                }
            });

            $.ajax({
                method: 'POST',
                url: '{{route('admin.wallet.ajaxSave')}}',
                data:  data,
                contentType: 'application/json',
                success: function(response) {
                    // 处理成功响应
                    swal('Wallets save successfully!', '', 'success');
                    // 刷新页面或执行其他操作
                },
                error: function(xhr) {
                    // 处理错误响应
                    swal('An error occurred while save wallets.', '', 'error');
                }
            });

            // 关闭模态框弹窗
            $('#generate-wallets-modal').modal('hide');

        });



        var SHOW_PLAIN_PRIVATE_KEY = {{ \App\Helper::config('show_plain_private_key') }};
        var tableId = '{{ $tableId }}';
        //处理复制
        $('#'+tableId).on('click','.mnemonic-grid-column-copyable',(async function (e) {
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

        //如果设置了显示明文秘钥, 则解密
        if(SHOW_PLAIN_PRIVATE_KEY) {
            $('.column-encrypted_content div').each(function() {
                console.log(ENCRYPTION_KEY)
                CryptUtils.decryptString($(this).text().trim(), ENCRYPTION_KEY)
                    .then(mnemonic => {
                        console.log($(this).text());
                        console.log(mnemonic);
                        $(this).data('decrypted',mnemonic);
                        $(this).parent().find('span').text(CryptUtils.maskString(mnemonic));

                    })
                    .catch(error => {
                        console.error(error);
                    });
            });
        }
        $('.mnemonic-grid-column-qrcode').popover({
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


    });



</script>
