<?php

namespace App\Admin\Controllers;

use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Layout\Content;

class QrcodeController extends AdminController
{
    //
    public function index(Content $content)
    {

        // 选填
        $content->title('字符转二维码');
        $content->view('tool.qrcode');
        return $content;
    }

    public function postQrCode(Content $content){

        $sourceText = \request()->get('sourceText');
        if (empty($sourceText)) {
            return '提交的文本不能为空!';
        }

        // 生成二维码图片
        $qrcode = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')->size(100)->generate($sourceText);

        // 显示二维码图片
        $src = 'data:image/png;base64,' . base64_encode($qrcode);
        $img = "<img src='$src' style='height:150px;width:150px;';>";


        $content->view('tool.qrcode',[
            'img'=> $img ,
        ]);

        return $content;
    }
}
