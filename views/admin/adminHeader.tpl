<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>{$pageTitle}</title>

    <link rel="stylesheet" href="{$templateWebPath}css/bootstrap.min.css" type="text/css">
    <link rel="stylesheet" href="{$templateWebPath}css/main.css" type="text/css">
    {*/ таблица стилей подключается изначально имея documentRoot в папке вебпространства ( 'www' ), а не любой другой *}

    <script type="text/javascript" src="/js/jquery-3.6.0.js"></script>
    <script type="text/javascript" src="/templates/admin/js/admin.js"></script>

</head>
<body>
<div id="header">
    <h1> управление сайтом </h1>
</div>



{include file="adminLeftcolumn.tpl"}

<div id="centerColumn">

