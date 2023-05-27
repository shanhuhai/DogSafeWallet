<?php

namespace App\Admin\Controllers;

use App\Group;
use Encore\Admin\Controllers\AdminController;
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

        $grid->column('id', __('Id'));
        $grid->column('name', __('Name'));
        $grid->column('mnemonic', __('Mnemonic'));
        $grid->column('wallet_count', __('Wallet count'));
        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));
        $grid->column('generate_wallets', 'Generate Wallets')->display(function () {
            return '<button class="btn btn-primary generate-wallets-btn" data-group-id="'.$this->id.'">Generate Wallets</button>';
        });
        $grid->footer(function ($query) {
            return view('group.modal');
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

        $form->text('name', __('Name'));
        $form->textarea('mnemonic', __('Mnemonic'));
        $form->text('wallet_count', __('Wallet count'));



        return $form;
    }

    public function generateWallets(){
        $groupId = request()->get('group_id');
        $walletCount = request()->get('wallet_count');
        $group = Group::find($groupId);

        $group->generateWallets($walletCount);

        return '0';
    }
}
