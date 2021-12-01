<?php


/**
 * 
 * ProductController.php
 * 
 * контроллер страницы товара  (/product/1)
 */


// подключаем модели

include_once "../models/CategoriesModel.php";
include_once "../models/ProductsModel.php";
include_once "Controller.php";

class ProductController extends AbstractController
{

    protected object $databaseObject; // объект класса базы данных
    protected object $categoriesModel; // модель
    protected object $smarty;
    protected object $productsModel;

    /**
     **
     * @param object $smarty объект смарти
     * @param object $databaseObject объект класса базы данных
     */
    public function __construct(object $smarty, object $databaseObject){
        $this->categoriesModel = new CategoriesModel($databaseObject);
        $this->smarty = $smarty;
        $this->databaseObject = $databaseObject;
        $this->productsModel = new ProductsModel($databaseObject);
    }

    /**
     * формирование страницы товара
     *
     * @GET integer id
     */
    public function indexAction()
    {
        $itemId = $_GET['id'] ?? null;
        if ($itemId == null) exit();

        // получить данные продукта
        $rsProduct = $this->productsModel->getProductById($itemId);
        // получить все категории
        $rsCategories = $this->categoriesModel->getAllMainCatsAndChildren();

        $this->smarty->assign('itemInCart', 0);
        if (in_array($itemId, $_SESSION['cart'])) {
            $this->smarty->assign('itemInCart', 1);
        }

        $this->smarty->assign('pageTitle', $rsProduct['name']);
        $this->smarty->assign('rsCategories', $rsCategories);
        $this->smarty->assign('rsProduct', $rsProduct);
        $this->smarty->assign('rsCategory', null);


        loadTemplate($this->smarty, 'product');
    }

}