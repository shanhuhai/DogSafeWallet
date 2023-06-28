<?php

namespace App\Admin\Controllers;

use App\Network;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class NetworkController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Network';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Network());

        $grid->column('id', __('Id'));
        $grid->column('name', __('Name'));
        $grid->column('rpc_url', __('Rpc url'));
        $grid->column('chain_id', __('Chain id'));
        $grid->column('currency_symbol', __('Currency symbol'));
        $grid->column('block_explorer_url', __('Block explorer url'));

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
        $show = new Show(Network::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('name', __('Name'));
        $show->field('rpc_url', __('Rpc url'));
        $show->field('chain_id', __('Chain id'));
        $show->field('currency_symbol', __('Currency symbol'));
        $show->field('block_explorer_url', __('Block explorer url'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Network());

        $form->text('name', __('Name'));
        $form->text('rpc_url', __('Rpc url'));
        $form->number('chain_id', __('Chain id'));
        $form->text('currency_symbol', __('Currency symbol'));
        $form->text('block_explorer_url', __('Block explorer url'));

        return $form;
    }
}
