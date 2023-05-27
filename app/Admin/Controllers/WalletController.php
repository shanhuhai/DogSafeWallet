<?php

namespace App\Admin\Controllers;

use App\Group;
use App\Wallet;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use App\Util;



function maskString($string, $starNum=3, $left=6, $right = 4)
{
    $length = strlen($string);
    if ($left==0 && $right==0) {
        return str_repeat('*', $starNum);
    }
    return substr($string, 0, $left) . str_repeat('*', $starNum) . substr($string, -$right);
}
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

        $grid->column('id', __('Id'));
        $grid->column('group.name',__('Group name'));
        $grid->column('address', __('Address'))->display(function($val){
            return maskString($val);
        })->qrcode();
        $grid->column('private_key', __('Private key'))->display(function($val,$column){
            return maskString($val,3,2,1);
        })->copyable();

        $grid->column('balance', __('Balance'))->display(function ($val, $column){
            return $val;
        });
        $grid->column('mnemonic', __('Mnemonic'));
        $grid->column('path', __('Path'));
        $grid->column('note', __('Note'));

        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));

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
        $show->field('mnemonic', __('Mnemonic'));
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
        $form->text('address', __('Address'));
        $form->text('private_key', __('Private key'));
        $form->textarea('mnemonic', __('Mnemonic'));
        $form->text('note',__('Note'));
        $form->text('path', __('Path'));

        $form->saving(function(Form $form){
            $form->private_key = Util::encryptString($form->private_key, Util::padKey(env('ENCRYPTION_KEY')));
        });
        return $form;
    }
}
