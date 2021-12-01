<head>
        <meta charset="UTF-8">
        <title>Авторизация в админ панель</title>

        <link rel="stylesheet" href="{$templateWebPath}css/bootstrap.min.css" type="text/css">
        <link rel="stylesheet" href="{$templateWebPath}css/main.css" type="text/css">
        {*/ таблица стилей подключается изначально имея documentRoot в папке вебпространства ( 'www' ), а не любой другой *}

        <script type="text/javascript" src="/js/jquery-3.6.0.js"></script>
        <script type="text/javascript" src="/templates/admin/js/admin.js"></script>

</head>

<h2>Авторизация в админ панель</h2>
<div>
        <span id="auth-error"></span>
        <input type="text" placeholder="имя" name="name" id="name">
        <input type="password" placeholder="пароль" name="password" id="password">
        <input type="button" onclick="adminAuth();" value="Войти">

</div>
