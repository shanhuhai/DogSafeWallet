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
    $router->post('/wallets/ajax-save','WalletController@ajaxSave')->name('wallet.ajaxSave');
    $router->resource('/wallets', 'WalletController');

    $router->resource('/wallet/groups', GroupController::class);
    $router->post('/wallet/mnemonic/generateWallets', 'MnemonicController@generateWallets')->name('mnemonic.generate_wallets');

    $router->resource('/wallet/mnemonics', MnemonicController::class);

    $router->get('/tool/qrcode', 'QrcodeController@index')->name('tool.qrcode.index');
    //Text to qrcode API
    $router->get('/tool/create_qrcode', 'QrcodeController@createQrcode')->name('tool.qrcode.create');

    $router->post('/wallet/pKToAddress', 'WalletController@pKToAddress')->name('wallet.pKToAddress');

    //用户设置
    $router->get('/user/config', 'AdminUserConfigController@index')->name('user.config');
    $router->post('/user/config/save', 'AdminUserConfigController@save')->name('user.config.save');

});
