<?php

namespace App\Admin\Controllers;

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
        // 创建一个表单
        $form = new Form(new Administrator());

        $config = Admin::user()->configs->pluck('value', 'key');


        $form->switch('show_plain_private_key', __('Show Plain Private Key'))->value($config['show_plain_private_key']);

        $form->switch('save_secret_key', __('Save secret Key'))->value($config['save_secret_key']);
        // 设置提交按钮
        $form->setAction(route('admin.user.config.save'));

        // 渲染表单
        $content->body($form);

        return $content;
    }

    public function save(Request $request)
    {
        // 保存配置项
        $data['show_plain_private_key'] = $request->input('show_plain_private_key');
        $data['save_secret_key'] = $request->input('save_secret_key');

        $userId = Admin::user()->id;

        foreach ($data as $key=>$val) {
            AdminUserConfig::updateOrCreate([
                'user_id'=>$userId,
                'key'=>$key,
                'value'=>$val == 'on'? 1: 0
            ]);
        }

        // 保存成功后的提示信息
        admin_success('Saved successfully.');

        // 跳转回设置页面
        return redirect()->to(route('admin.user.config'));
    }
}
