<?php
/**
 * файл настроек
 */
//> константы для обращения к контроллерам
    const CONTROLLER_PATH_PREFIX = "../controllers/";
    const CONTROLLER_PATH_POSTFIX = "Controller.php";
    const CLASS_POSTFIX = "Controller";
//< 



//> используемый шаблон
    $template = 'cosmo'; // название шаблона
    $templateAdmin = 'admin';
    /**
     * пути к файлам шаблонов ( .tpl )
     */

    define('TEMPLATE_PATH_PREFIX', "../views/$template/");
    define('TEMPLATE_ADMIN_PATH_PREFIX', "../views/{$templateAdmin}/");
    const TEMPLATE_PATH_POSTFIX = ".tpl";
    
    /**
     * пути к файлам шаблонов в вебпространсте
     * это подключаемые файлы ( css)
     */
    define('TEMPLATE_WEBPATH', "/templates/$template/");
    define('TEMPLATE_ADMIN_WEBPATH', "/templates/$templateAdmin/");
    
//<

//> инициализация шаблонизатора Smarty

require('../library/Smarty/libs/Smarty.class.php');

$smarty = new Smarty();

$smarty->setTemplateDir(TEMPLATE_PATH_PREFIX); // указываем шаблонизатору где хранятся наши шаблоны
$smarty->setCompileDir("../tmp/smarty/templates_c"); // указываем шаблонизатору куда сохранять новые скомпиленные шаблоны
$smarty->setCacheDir("../tmp/smarty/cache"); // кэш
$smarty->setConfigDir('../library/Smarty/configs'); // установка папки с конфигом

$smarty->assign('templateWebPath', TEMPLATE_WEBPATH);

//<