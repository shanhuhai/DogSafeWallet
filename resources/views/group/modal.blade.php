<div class="modal fade" id="generate-wallets-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Generate Wallets</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <label for="wallet-count-input">Wallet Count:</label>
                <input type="number" id="wallet-count-input" min="1" step="1">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button id="submit-btn" type="button" class="btn btn-primary">Submit</button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(function(){

        var groupId ;
        $(document).on('click', '.generate-wallets-btn', function() {
             groupId = $(this).data('group-id');

            // 显示模态框弹窗
            $('#generate-wallets-modal').modal('show');

        });


        $('#submit-btn').on('click', function() {
            var walletCount = parseInt($('#wallet-count-input').val());

            if (isNaN(walletCount) || walletCount <= 0) {
                alert('Invalid wallet count. Please enter a positive number.');
                return;
            }
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{{csrf_token()}}'
                }
            });
            // 使用 AJAX 请求后端接口生成钱包并保存到 wallets 表中
            $.ajax({
                method: 'POST',
                url: '{{route('admin.group.generate_wallets')}}',
                data: {
                    group_id: groupId,
                    wallet_count: walletCount
                },
                success: function(response) {
                    // 处理成功响应
                    alert('Wallets generated successfully!');
                    // 刷新页面或执行其他操作
                },
                error: function(xhr) {
                    // 处理错误响应
                    alert('An error occurred while generating wallets.');
                }
            });

            // 关闭模态框弹窗
            $('#generate-wallets-modal').modal('hide');
        });

    });
    // 处理提交按钮的点击事件


</script>
