
## 简介

狗狗安全钱包管理器是一个在线管理系统，用于解决您的钱包过多时，钱包的分组管理、以及在各个网络下各种资产的批量查看与统计。

## 安全说明
- 钱包管理，安全是重中之重。我们对您的助记词和钱包私钥进行了对称加密处理。
- 您只需要在一开始设置一个加密秘钥，用于加密解密您的助记词和私钥。 加密秘钥存储在您的浏览器本地，不会触网。
- 加密解密过程完全在前端进行，后端不会接触您的明文助记词和私钥。
- 您需要自行保管好该秘钥就可以管理您所有的钱包。
- 加密算法使用OpenSSL 目前最强的 AES 对称加密算法。

## 主要功能

- 助记词导入、助记词生成、
- 助记词加密、生成明文助记词二维码、生成加密助记词二维码
- 根据助记词批量生成钱包
- 钱包私钥加密、钱包明文私钥二维码，钱包私钥加密二维码
- 钱包分组管理
- 支持选择是否以明文显示私钥和助记词
- 支持添加网络
- 支持用户选择显示哪些网络下的代币余额


## 未来规划
- [ ] 添加网络下代币， 并显示指定网络下的代币余额。
- [ ] 资产转移，可以直接从欧易， 币安等交易所，直接转移到自己的私有钱包中。
- [ ] 资产统计面板
- [ ] 一键托管加密密钥到Google 云盘、百度网盘


## 安装

### 手动安装

下载代码  git clone https://github.com/shanhuhai/DogSafeWallet.git

配置Nginx：

配置.env：

    cp .env.example .env

修改 .env 中的 `APP_URL` 为你的网站地址。

```
APP_URL=https://wallet.dognav.com
```

修改 .env 中的数据库配置：

```
DB_HOST=127.0.0.1  //数据库IP
DB_PORT=3306   // 数据库端口
DB_DATABASE=DogSafeWallet  //数据库名称
DB_USERNAME=root   //数据库用户名
DB_PASSWORD=123456  //数据库密码
```

修改目录权限

```
chmod -R 755 storage/
```

创建上传目录

```
mkdir public/upload/images
chmod -R 755 public/upload/images
```

导入数据库



### Docker安装


## 参考资料
各币种钱包索引代号
https://github.com/satoshilabs/slips/blob/master/slip-0044.md
