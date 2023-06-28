<?php

namespace App\Admin\Controllers;

use App\Helper;
use App\Network;
use Encore\Admin\Auth\Database\Administrator;
use Illuminate\Http\Request;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Layout\Content;
use App\AdminUserConfig;
use Encore\Admin\Facades\Admin;

class AdminUserConfigController extends AdminController
{
    public function index(Content $content)
    {

        // 渲染表单
        $content->body($this->form());

        return $content;
    }

    protected function form(){
        // 创建一个表单
        $form = new Form(new Administrator());

        $form->switch('show_plain_private_key', __('Show Plain Private Key'))->value(Helper::config('save_secret_key',0));
        // $form->switch('save_secret_key', __('Save secret Key'))->value(Helper::config('save_secret_key',0));
        // 设置提交按钮
        $form->setAction(route('admin.user.config.save'));
        $form->disableEditingCheck();
        $form->disableCreatingCheck();
        $form->disableViewCheck();
        $form->tools(function (Form\Tools $tools) {
            $tools->disableDelete();
            $tools->disableView();
            $tools->disableList();
        });

        $form->listbox('networks', __('Networks'))->options(Network::all()->pluck('name', 'id'))->value(Admin::user()->networks->pluck('id')->toArray());
        return $form;
    }

    public function save(Request $request)
    {
        // 保存配置项
        $data['show_plain_private_key'] = $request->input('show_plain_private_key');
        $data['save_secret_key'] = $request->input('save_secret_key');

        $userId = Admin::user()->id;

        foreach ($data as $key=>$val) {
            AdminUserConfig::updateOrCreate(['user_id'=>$userId, 'key'=>$key],[
                'value'=>$val == 'on'? 1: 0
            ]);
        }

        $user = Admin::user();
        $networks = $request->input('networks');
        $toSave= [];
        foreach ($networks as $network) {
            if (!is_null($network)) $toSave[] = $network;
        }

        // 将用户新选择的网络关联起来
        if ($request->has('networks')) {
           $user->networks()->sync($toSave);

        }

        // 保存成功后的提示信息
        admin_success(__('Saved successfully'));
        // 跳转回设置页面
        return redirect()->to(route('admin.user.config'));
    }
}
