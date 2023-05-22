<?php

namespace App\Admin\Controllers;

use App\Wallet;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;




function replaceStringAfterIndex($string, $index)
{
    $length = strlen($string);

    if ($index >= $length) {
        return $string;
    }

    $replacement = str_repeat('*', $length - $index-30);
    $result = substr_replace($string, $replacement, $index);

    return $result;
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
        $grid->column('address', __('Address'))->qrcode();
        $grid->column('private_key', __('Private key'))->display(function($val,$column){
            return replaceStringAfterIndex($val,  5);
        })->qrcode();

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

        $form->text('address', __('Address'));
        $form->text('private_key', __('Private key'));
        $form->textarea('mnemonic', __('Mnemonic'));
        $form->text('note',__('Note'));
        $form->text('path', __('Path'));

        return $form;
    }
}
