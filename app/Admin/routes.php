<?php

use Illuminate\Routing\Router;

Admin::routes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
    'as'            => config('admin.route.prefix') . '.',
], function (Router $router) {

    $router->get('/', 'HomeController@index')->name('home');
    $router->resource('/wallets', 'WalletController');

    $router->resource('/wallet/groups', GroupController::class);
    $router->post('/wallet/group/generateWallets', 'GroupController@generateWallets')->name('group.generate_wallets');


    $router->get('/tool/qrcode', 'QrcodeController@index')->name('tool.qrcode.index');
    $router->post('/tool/qrcode', 'QrcodeController@postQrcode')->name('tool.qrcode.post');

    $router->post('/wallet/pKToAddress', 'WalletController@pKToAddress')->name('wallet.pKToAddress');

});
