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

        $grid->column(env('PLAIN_PRIVATE_KEY') ? 'decrypted_private_key': 'private_key', __('Private key'))->display(function($val,$column){
            return Helper::maskString($val);
        })->copyable()->qrcode();

        $grid->column('balance', __('Balance'))->display(function ($val, $column){
            return $val;
        });
        $grid->column('zksBalance', __('zksBalance'))->display(function ($val, $column){
            return $val;
        });
        $grid->column('path', __('Path'));
        $grid->column('note', __('Note'));

        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));

        $grid->footer(function($query){
            return view('wallet.gridFooter');
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

        $form->saving(function(Form $form){
            $form->private_key = Helper::encryptString($form->private_key, Helper::padKey(env('ENCRYPTION_KEY')));
        });



        Admin::script(view('wallet.formScript')->render());
//        $form->footer(function ($footer){
//            return $footer->render().view('wallet.formScript')->render();
//        });
        return $form;
    }

    public function pKToAddress(){
        return [
            'code'=>0,
            'address'=>Helper::getAddressFromPrivateKey(\request()->get('pk'))
        ];
    }
}
