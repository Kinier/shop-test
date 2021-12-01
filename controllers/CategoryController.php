<?php

/**
 * CategoryController.php
 * 
 * контроллер страницы категорий (/category/1)
 */


//>подключаем модели
include "../models/CategoriesModel.php";
include "../models/ProductsModel.php";
include "Controller.php";
//<
class CategoryController extends AbstractController
{
    private array|null $rsChildCats;
    private array|bool|null $rsProducts;
    private ?int $catId;
    protected object $databaseObject; // объект класса базы данных
    protected object $model; // модель
    protected object $smarty;
    //private object $productsModel;
    protected object $productsModel;

    /**
     * @param object $smarty объект смарти
     * @param object $databaseObject объект класса базы данных
     */
    function __construct(object $smarty, object $databaseObject){
        $this->rsChildCats = null;
        $this->rsProducts = null;
        $this->catId = $_GET['id'] ?? null;
        $this->model = new CategoriesModel($databaseObject); // TODO НУЖНО ПЕРЕДАВАТЬ ОБЪЕКТ КЛАССА  ( $model_object($databaseObject)
        $this->smarty = $smarty;
        $this->databaseObject = $databaseObject;
    }
    /**
     * формирование страницы категории
     *
     *
     */
    public function indexAction(): void
    {



        if ($this->catId == null) exit();

        $rsCategory = $this->model->getCatById($this->catId); // больше не юзается

        //$db = getAndCreateDbConnection();


        if ($rsCategory['parent_id'] == 0) {
            $this->rsChildCats = $this->model->getChildrenForCat($this->catId);// юзается в модели какой-то // FIXME: поменять ( что? )

        } else {
            $this->productsModel = new ProductsModel($this->databaseObject); // todo РЕШИТЬ блин ДИЛЕМУ эдакую!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
            $this->rsProducts = $this->productsModel->getProductsByCat($this->catId); // больше не юзается

        }

        $rsCategories = $this->model->getAllMainCatsAndChildren(); // юзается 6 раз в разных файлах

        $this->smarty->assign('pageTitle', 'Товары категории ' . $rsCategory['name']);

        $this->smarty->assign('rsCategory', $rsCategory);
        $this->smarty->assign('rsProducts', $this->rsProducts);
        $this->smarty->assign('rsChildCats', $this->rsChildCats);

        $this->smarty->assign('rsCategories', $rsCategories);
        loadTemplate($this->smarty, 'category');

    }

}