<?php

/**
 * основные функции
 */

use JetBrains\PhpStorm\NoReturn;

/**
 * формирование запрашиваемой страницы
 *
 * @param object $smarty настроенный объект smarty
 * @param string $controllerName название контроллера
 * @param string $actionName название функции обработки страницы
 */

function loadPage(object $smarty, string $controllerName, string $actionName = 'index'){

    include_once CONTROLLER_PATH_PREFIX . $controllerName . CONTROLLER_PATH_POSTFIX;

    $controllerName .= CLASS_POSTFIX;
    $databaseObject = new DatabaseConnectPDO(); // mb to do connection instead of object
    $class = new $controllerName($smarty, $databaseObject);

    $action = $actionName . 'Action';

    $class->$action($smarty);
    //$action($smarty);
}

/**
 * эти функции здесь, а не в контроллере, потому что схуяли епта)
 * ЭТИ ОБЩИЕ ФУНКЦИИ ИСПОЛЬЗУЮТСЯ ДАЖЕ С ДРУГИМИ КОНТРОЛЛЕРАМИ
 * А ИНДЕКСэкшн ФУНКЦИЯ В КАЖДОМ КОНТРОЛЛЕРЕ СВОЯ, И УЖЕ ОНА ЗАПУСКАЕТ НУЖНЫЕ ЕЙ ЗДЕСЬ ФУНКЦИИ
 */



/**
 * загрузка шаблона 
 *
 * @param object $smarty объект шаблонизатора
 * @param string $templateName имя шаблона
 */
function loadTemplate(object $smarty, string $templateName)
{
    $smarty->display($templateName . TEMPLATE_PATH_POSTFIX);
}


/**
 * Функция отладки. Если $die равна нулю, 
 * то выполнение функции не остановит работу программы
 *
 * @param mixed|null $value переменная выводящаяся на страницу
 * @param  mixed $die завершиться программе или нет
 */
function d(mixed $value = null, mixed $die = 1)
{

    function debugOut($a){
        echo '<br><b>'. basename($a['file']). '</b>'
         . "&nbsp;&nbsp; <b style='color:#ff0000'>({$a['line']})</b>"
        . "&nbsp; <b style = 'color:#0037ff'>{$a['function']}</b>"
            . "&nbsp; -- ". dirname($a['file']);
    }
    echo '<pre>';
        $trace = debug_backtrace();
        array_walk($trace, 'debugOut');
    echo "\n\n";
        print_r($value);
    echo '</pre>';

    if ($die) die; // умирает по умолчанию
}


/**
 * createSmartyRsArray
 *
 * @param mixed $rs сделанный запрос в базу данных, готовый через ->fetch отдавать данные
 * @return bool|array многомерный массив
 */
function createSmartyRsArray(mixed $rs): bool|array
{
    if (!$rs) return false;

    $smartyRs = array();
    while($row = $rs->fetch()){
        $smartyRs[] = $row;
    }
  
    return $smartyRs;
}


/**
 * функция перенаправления
 */

 #[NoReturn] function redirect($url = '/'){
    header("Location: $url");
    exit;
 }