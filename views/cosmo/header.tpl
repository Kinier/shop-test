<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <title>{$pageTitle}</title>


    <link rel="stylesheet" href="{$templateWebPath}css/bootstrap.min.css" type="text/css">
    <link rel="stylesheet" href="{$templateWebPath}css/main.css" type="text/css">
    {*/ таблица стилей подключается изначально имея documentRoot в папке вебпространства ( 'www' ), а не любой другой *}

    <script type="text/javascript" src="/js/jquery-3.6.0.js"></script>
    <script type="text/javascript" src="/js/main.js"></script>

</head>
<body>
<div id="header">
    <h1 class="card-header">
        {if !$rsCategory}
            {textformat}myshop - магазин товаров{/textformat}
        {else}
            {$rsCategory['name']}
        {/if}
    </h1>
</div>


{include file="leftcolumn.tpl"}

<div id="centerColumn">
