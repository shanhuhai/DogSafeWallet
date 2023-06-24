<?php

namespace App\Admin\Controllers;

use App\Group;
use App\Wallet;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use App\Helper;


class WalletController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Wallet';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
         Helper::config('show_plain_private_key');

        $grid = new Grid(new Wallet());
        $grid->filter(function($filter){
            $filter->disableIdFilter();
            // 在这里添加字段过滤器
            $filter->like('address', __('Address'));
        });

        $grid->selector(function (Grid\Tools\Selector $selector) {
            $groups = Group::all();
            $selector->selectOne('group_id', '分组', $groups->pluck('name', 'id'));
        });

        $grid->model()->orderBy('id', 'desc');

        $grid->column('id', __('Id'));
        $grid->column('group.name',__('Group name'));
        $grid->column('address', __('Address'))->display(function($val){
            $val = mb_strtolower($val);
            return Helper::maskString($val)."<span style='display: none' class='hidden-address'> $val </span>";
        })->copyable()->qrcode();

        $grid->column( 'encrypted_private_key', __('Private key'))->display(function($val,$column){
            $return = <<<HTML
<a href="javascript:void(0);" class="private-key-grid-column-qrcode text-muted"  data-toggle='popover' tabindex='0'>
    <i class="fa fa-qrcode"></i>
</a>&nbsp;
HTML;

            $return .=  "<span>".Helper::maskString($val)."</span>"."<div style='display: none'>$val</div>";
            //复制按钮
            return $return.'<a href="javascript:void(0);" class="private-key-grid-column-copyable text-muted" data-content="'.$val.'" title="Copied!" data-placement="bottom">
    <i class="fa fa-copy"></i>';
        });

        $grid->column('balance', __('Balance'))->display(function ($val, $column){
            return $val;
        });
        $grid->column('zksBalance', __('zksBalance'))->display(function ($val, $column){
            return $val;
        });
        $grid->column('path', __('Path'));
        $grid->column('note', __('Note'));

        $grid->footer(function($query) use ($grid) {
            return view('wallet.gridFooter')->with('tableId',$grid->tableID);
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
        $show = new Show(Wallet::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('address', __('Address'));
        $show->field('private_key', __('Private key'));
        $show->field('path', __('Path'));
        $show->field('note', __('Note'));
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
        $form = new Form(new Wallet());
        $groups = Group::all();

        $form->select('group_id', '分组')->options($groups->pluck('name', 'id'));
        $form->text('private_key', __('Private key'));

        $form->text('address', __('Address'));
        $form->text('note',__('Note'));
        $form->text('path', __('Path'));
        $form->hidden('encrypted_private_key', 'Encrypted private key');

        $form->ignore(['private_key']);


        $form->footer(function ($footer) {
            // 去掉`查看`checkbox
            $footer->disableViewCheck();

            // 去掉`继续编辑`checkbox
            $footer->disableEditingCheck();

            // 去掉`继续创建`checkbox
            $footer->disableCreatingCheck();

        });


        Admin::js('js/app.js');
        Admin::script(view('wallet.formScript')->render());

        return $form;
    }

    public function pKToAddress(){
        return [
            'code'=>0,
            'address'=>Helper::getAddressFromPrivateKey(\request()->get('pk'))
        ];
    }

    public function ajaxSave(){
        $wallets = request()->json('wallets');
        $mnemonicId = request()->json('mnemonic_id');
        $groupId = request()->json('group_id');
        // 遍历钱包数据并存入数据库
        foreach ($wallets as $wallet) {
            Wallet::create([
                'mnemonic_id'=>$mnemonicId,
                'group_id'=>$groupId,
                'encrypted_private_key' => $wallet['privateKey'],
                'address' => $wallet['address'],
            ]);
        }

        return response()->json(['message' => '钱包数据已成功保存']);
    }
}
