
    $(function(){


        $('#private_key').on('input', function() {
            var privateKey = $(this).val();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{{csrf_token()}}'
                }
            });
            // 发送 AJAX 请求导入钱包并获取地址
            $.ajax({
                url: '{{route('admin.wallet.pKToAddress')}}',
                type: 'POST',
                data: { pk: privateKey },
                dataType: 'json',
                success: function(response) {
                    // 将钱包地址填充到地址输入框
                    $('#address').val(response.address);
                }
            });
        });
    })

