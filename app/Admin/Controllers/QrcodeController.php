<?php

namespace App\Admin\Controllers;

use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Layout\Content;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Response;

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

    public function createQrcode(){

        $text = request('text', 'Hello, World!'); // 默认文本为 "Hello, World!"
        $size = request('size', 200); // 默认大小为 200


        $text = rawurldecode($text);
        // 生成二维码图片
        $qrCode = QrCode::format('png')->size($size)->generate($text);

        // 设置响应头为 image/png
        $response = Response::make($qrCode, 200);
        $response->header('Content-Type', 'image/png');

        // 设置缓存过期时间为1天
        $response->header('Cache-Control', 'public');
        $response->header('Expires', now()->addDay()->format('D, d M Y H:i:s T'));

        return $response;
    }
}
