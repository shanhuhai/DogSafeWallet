<?php

namespace App\Admin\Controllers;

use App\Group;
use App\Helper;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class GroupController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Group';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Group());
        $grid->disableExport();

        $grid->model()->where('user_id', Admin::user()->id)->orderBy('id', 'desc');
        $grid->column('id', __('Id'));
        $grid->column('name', __('Name'));
        $grid->column('wallet_count', __('Wallet count'))->display(function(){
            // 查询当前分组下的钱包数量
            $walletCount = $this->wallets()->count();
            return $walletCount;
        });
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
        $show = new Show(Group::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('name', __('Name'));
        $show->field('mnemonic', __('Mnemonic'));
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
        $form = new Form(new Group());
        $form->disableEditingCheck();
        $form->disableCreatingCheck();
        $form->disableViewCheck();
        $form->text('name', __('Name'));
     //   $form->textarea('mnemonic', __('Mnemonic'));

//        $form->saving(function(Form $form){
//            $form->mnemonic = Helper::encryptString($form->mnemonic, Helper::padKey(env('ENCRYPTION_KEY')));
//        });
        $form->hidden('user_id')->value(Admin::user()->id);
        return $form;
    }


}
