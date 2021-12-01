<?php

/**
 * Контроллер главной страницы
 */

// подключаем модели
include_once "../models/CategoriesModel.php";
include_once "../models/ProductsModel.php";

include_once "Controller.php";


class IndexController extends AbstractController
{
    protected object $databaseObject; // объект класса базы данных
    protected object $categoriesModel; // модель
    protected object $smarty;
    //private object $productsModel;
    protected object $productsModel;
/**
**
* @param object $smarty объект смарти
* @param object $databaseObject объект класса базы данных
*/
    public function __construct(object $smarty, object $databaseObject){
        $this->categoriesModel = new CategoriesModel($databaseObject); // TODO НУЖНО ПЕРЕДАВАТЬ ОБЪЕКТ КЛАССА  ( $model_object($databaseObject)
        $this->smarty = $smarty;
        $this->databaseObject = $databaseObject;
        $this->productsModel = new ProductsModel($databaseObject);
    }

    /**
     * Работа с информацией для главной страницы сайта
     */
    public function indexAction()
    {
        $rsCategories = $this->categoriesModel->getAllMainCatsAndChildren();
        //> пагинатор
        $paginator = array();
        $paginator['perPage'] = 9; // элементов на странице
        $paginator['currentPage'] = $_GET['page'] ?? 1; // на какой странице находимся, если не пришло, значит на первой
        //> формируем инфу с какого индекса начать выборку в базе данных
        $paginator['offset'] = ($paginator['currentPage']) * $paginator['perPage'] - $paginator['perPage'];
        //< текущая страница умножить на кол-во товара на странице, затем минусуем кол-во товара на странице, и получается нужный офсет
        $paginator['link'] = '/index/?page=';
        list($rsProducts, $allCnt) = $this->productsModel->getLastProducts($paginator['offset'], $paginator['perPage']);

        $paginator['pageCnt'] = ceil($allCnt / $paginator['perPage']); // получаем сколько у нас всего страниц будет
        $this->smarty->assign('paginator', $paginator);
        //<


        // rs - record set - набор данных категорий
        $this->smarty->assign('rsCategories', $rsCategories);
        $this->smarty->assign('pageTitle', 'Главная страница сайта');
        $this->smarty->assign('rsProducts', $rsProducts);
        $this->smarty->assign('rsCategory', null);


        loadTemplate($this->smarty, 'index');
    }



}
// 