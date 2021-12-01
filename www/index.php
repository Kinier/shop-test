<?php

session_start(); // старт сессии

if (!isset($_SESSION['cart'])) // если в сессии не массива корзины, то создаем
    $_SESSION['cart'] = array();

/** @var object $smarty */ // из config.php
include_once "../config/config.php"; // настройки
include_once "../config/db.php"; // инициализация базы данных
include_once "../library/mainFunctions.php"; // основные функции


 // возможно потом добавить чтобы можно было использовать другие базы данных
    // определяем с каким контроллером работать
    $controllerName = isset($_GET['controller']) ? ucfirst($_GET['controller']) : 'Index'; 


    // определяем с какой функцией работать
    $actionName = $_GET['action'] ?? 'index';

    // если в сессии есть данные об авторизованном пользователе, то передаем их в шаблон
    if (isset($_SESSION['user'])){ // проверка проходит если переменной нету или она равна NULL, данные о юзере удаляются через unset

        $smarty->assign('arUser', $_SESSION['user']);
    }

    if ($controllerName == 'Admin'){
        if (isset($_SESSION['admin'])) { // проверка проходит если переменной нету или она равна NULL, данные о юзере удаляются через unset
            $smarty->assign('arAdmin', $_SESSION['admin']);
            $smarty->assign('isAdmin', true);
            loadPage($smarty, $controllerName, $actionName);
            exit();
        }
        // todo переделать немного эти условия( возможно чтобы лучше выглядело )

        if(isset($_POST['name'], $_POST['password'])){
            loadPage($smarty, $controllerName, 'auth');
        }else{
            $smarty->assign('isAdmin', false);
            loadPage($smarty, $controllerName, 'login');
        }
        exit();

    }
    
    $smarty->assign('cartCntItems', count($_SESSION['cart']));


    loadPage($smarty, $controllerName, $actionName);