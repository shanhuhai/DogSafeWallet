<?php

namespace App\Admin\Controllers;

use App\Group;
use App\Helper;
use App\Mnemonic;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class MnemonicController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Mnemonic';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Mnemonic());
        $grid->disableExport();

        $grid->model()->where('user_id', Admin::user()->id)->orderBy('id', 'desc');
        $grid->column('id', __('Id'));

        $SHOW_PLAIN_PRIVATE_KEY = Helper::config('show_plain_private_key');
        $grid->column('encrypted_content', $SHOW_PLAIN_PRIVATE_KEY?__('Mnemonic'):__('Mnemonic(Encrypted)'))->display(function ($val){
            $return = <<<HTML
<a href="javascript:void(0);" class="mnemonic-grid-column-qrcode text-muted"  data-toggle='popover' tabindex='0'>
    <i class="fa fa-qrcode"></i>
</a>&nbsp;
HTML;
            $return .=  "<span>".Helper::maskString($val)."</span>"."<div style='display: none'>$val</div>";
            //复制按钮
            return $return.'<a href="javascript:void(0);" class="mnemonic-grid-column-copyable text-muted" data-content="'.$val.'" title="Copied!" data-placement="bottom">
    <i class="fa fa-copy"></i>';
        });
        $grid->column('wallet_count', __('Wallet count'))->display(function(){
            // 查询当前分组下的钱包数量
            $walletCount = $this->wallets()->count();
            return $walletCount;
        });


        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));
        $grid->column('generate_wallets', __('Generate wallets'))->display(function () {
            return '<button class="btn btn-primary generate-wallets-btn" data-mnemonic-id="'.$this->id.'" data-encrypted-mnemonic="'.$this->encrypted_content.'">'. __('Generate wallets') .'</button>';
        });
        $grid->footer(function ($query) use($grid,$SHOW_PLAIN_PRIVATE_KEY){
            $groups = Helper::groups();
            return view('mnemonic.modal')
                ->with('groups',$groups)
                ->with('tableId',$grid->tableID)
                ->with('SHOW_PLAIN_PRIVATE_KEY', $SHOW_PLAIN_PRIVATE_KEY);
        });
        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(Mnemonic::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('encrypted_content', __('Encrypted content'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Mnemonic());

        $form->text('content',  __('Mnemonic'))->append('<button id="generate-mnemonic-btn" class="btn btn-success btn-xs">生成</button>');
        $form->text('encrypted_content', __('Encrypted mnemonic'));


        $UID = Admin::user()->id;
        $form->hidden('user_id')->value($UID);
        Admin::js('/js/app.js');
        $keyString = env('ENCRYPTION_KEY');
        $script =<<<ETO

 var ENCRYPTION_KEY = CryptUtils.getEncryptionKey($UID);
 $(function() {
        // 绑定生成助记词按钮的点击事件
        $('#generate-mnemonic-btn').click(async function(e) {
            e.preventDefault();
            // 调用生成助记词的函数
            await generateMnemonic();
            return false;
        });

        // 生成助记词的函数
        async function generateMnemonic() {
            // 使用 bip39 生成助记词
            let mnemonic = generateRandomMnemonic(12);
          ;
            let encryptString = await CryptUtils.encryptString(mnemonic,ENCRYPTION_KEY);
            // 将助记词填入输入框
            $('#encrypted_content').val(encryptString);
            $('#content').val(mnemonic);
        }
        $('#content').change(async function(){
            let mnemonic =  $(this).val()
            let encryptString = await CryptUtils.encryptString(mnemonic,ENCRYPTION_KEY);
            $('#encrypted_content').val(encryptString);
        });

        // 生成指定长度的随机助记词
        function generateRandomMnemonic(length) {

          // 使用 bip39 包生成助记词
          var mnemonic = bip39.generateMnemonic(128); // 128 为熵的位数，可以根据需要调整
           return mnemonic
        }
    });

ETO;
        Admin::script($script);


        $form->footer(function ($footer) {
            // 去掉`查看`checkbox
            $footer->disableViewCheck();

            // 去掉`继续编辑`checkbox
            $footer->disableEditingCheck();

            // 去掉`继续创建`checkbox
            $footer->disableCreatingCheck();

        });

        //不保存 content
        $form->ignore(['content']);
        return $form;
    }

    public function generateWallets(){
        $mnemonicId = request()->get('mnemonic_id');
        $walletCount = request()->get('wallet_count');
        $groupId = request()->get('group_id', 1);
        $mnemonic = Mnemonic::find($mnemonicId);

        $mnemonic->generateWallets($walletCount, $groupId);

        return '0';
    }
}
