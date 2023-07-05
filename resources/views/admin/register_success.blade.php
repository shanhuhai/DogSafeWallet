<!DOCTYPE html>
<html>
<head>
    <title>注册成功</title>
    <!-- Bootstrap 3.3.5 -->
    <link rel="stylesheet" href="{{ admin_asset("vendor/laravel-admin/AdminLTE/bootstrap/css/bootstrap.min.css") }}">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ admin_asset("vendor/laravel-admin/font-awesome/css/font-awesome.min.css") }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ admin_asset("vendor/laravel-admin/AdminLTE/dist/css/AdminLTE.min.css") }}">
    <!-- iCheck -->
    <link rel="stylesheet" href="{{ admin_asset("vendor/laravel-admin/AdminLTE/plugins/iCheck/square/blue.css") }}">
    <style>
        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .content {
            text-align: center;
            padding: 20px;
            width: 60%;

            background-color: #f7f7f7;
        }
    </style>
    <script>
        setTimeout(function () {
            window.location.href = "{{ route('admin.login') }}";
        }, 3000);
    </script>
</head>
<body class="skin-blue">
<div class="container">
    <div class="content">
        <h1>注册成功</h1>

        <p>{{ session('success') }}</p>

        <a  href="{{ route('admin.login') }}"  class="btn btn-primary countdown"><span id="countdown">3</span> 秒后跳转..</a>
    </div>
</div>

<script>
    // 倒计时逻辑
    var countdown = 3;
    var countdownElement = document.getElementById('countdown');

    var countdownInterval = setInterval(function () {
        countdown--;
        countdownElement.innerText = countdown;

        if (countdown <= 0) {
            clearInterval(countdownInterval);
        }
    }, 1000);
</script>
</body>
</html>
