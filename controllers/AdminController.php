<?php

/*
 * AdminController.php
 *
 * контроллер админки сайта
 */

// подключаем модели
include_once '../models/CategoriesModel.php';
include_once '../models/ProductsModel.php';
include_once  '../models/OrderModel.php';
include_once '../models/PurchaseModel.php';
include_once '../models/AdminModel.php';

include_once 'Controller.php';

/** @noinspection PhpUndefinedVariableInspection */
$smarty->setTemplateDir(TEMPLATE_ADMIN_PATH_PREFIX); // указываем шаблонизатору где хранятся наши шаблоны
$smarty->assign('templateWebPath', TEMPLATE_ADMIN_WEBPATH);

class AdminController extends AbstractController
{
    protected object $databaseObject; // объект класса базы данных
    protected object $categoriesModel; // модель
    protected object $smarty;
    protected object $productsModel;
    //protected object $usersModel;
    protected object $purchaseModel;
    protected object $ordersModel;
    protected object $adminModel;

    /**
     **
     * @param object smarty объект смарти
     * @param object $databaseObject объект класса базы данных
     */
    public function __construct(object $smarty, object $databaseObject){
        $this->categoriesModel = new CategoriesModel($databaseObject);
        $this->smarty = $smarty;
        $this->databaseObject = $databaseObject;
        $this->productsModel = new ProductsModel($databaseObject);
        $this->purchaseModel = new PurchaseModel($databaseObject);
        $this->ordersModel = new OrderModel($databaseObject);
        $this->adminModel = new AdminModel($databaseObject);
    }


    public function indexAction(): void
    {


        $rsCategories = $this->categoriesModel->getAllMainCategories();


        $this->smarty->assign('rsCategories', $rsCategories);
        $this->smarty->assign('pageTitle', 'Управление сайтом');

        loadTemplate($this->smarty, 'adminHeader');
        loadTemplate($this->smarty, 'admin');
        loadTemplate($this->smarty, 'adminFooter');

    }

    /**
     *  Добавление новой категории
     */
    public function addnewcatAction() : void
    {
        $catName = $_POST['newCategoryName'];
        $catParentId = $_POST['generalCatId'];

        $res = $this->categoriesModel->insertCat($catName, $catParentId);
        if ($res) {
            $resData['success'] = 1;
            $resData['message'] = 'Категория добавлена';
        } else {
            $resData['success'] = 0;
            $resData['message'] = 'Ошибка добавления категории';
        }
        echo json_encode($resData);
        return;
    }

    /**
     * Страница управления категориями
     */
    public function categoryAction() : void
    {
        $rsCategories = $this->categoriesModel->getAllCategories();

        $rsMainCategories = $this->categoriesModel->getAllMainCategories();

        $this->smarty->assign('rsCategories', $rsCategories);
        $this->smarty->assign('rsMainCategories', $rsMainCategories);
        $this->smarty->assign('pageTitle', "Управление сайтом");


        loadTemplate($this->smarty, 'adminCategory');


    }


    /**
     *  Изменение категории
     */
    public function updatecategoryAction() : void
    {
        $itemId = $_POST['itemId'];
        $parentId = $_POST['parentId'];
        $newName = $_POST['newName'];

        $res = $this->categoriesModel->updateCategoryData($itemId, $parentId, $newName);
        if ($res) {
            $resData['success'] = 1;
            $resData['message'] = 'Категория обновлена';
        } else {
            $resData['success'] = 0;
            $resData['message'] = 'Ошибка изменения данных категории';
        }
        echo json_encode($resData);
        return;
    }

    /**
     * Страница управления товарами
     *
     * @param $this->smarty
     */
    public function productsAction() : void
    {
        $rsCategories = $this->categoriesModel->getAllCategories();
        $rsProducts = $this->productsModel->getProducts();

        $this->smarty->assign('rsCategories', $rsCategories);
        $this->smarty->assign('rsProducts', $rsProducts);

        $this->smarty->assign('pageTitle', 'управление сайтом');

        loadTemplate($this->smarty, 'adminProducts');
    }

    /**
     *  Добавить новый продукт
     * @echo json массив о результате действия ( успешно или ошибка )
     */
    public function addproductAction() :void
    {
        $itemName = $_POST['itemName'];
        $itemPrice = $_POST['itemPrice'];
        $itemDesc = $_POST['itemDesc'];
        $itemCat = $_POST['itemCatId'];


        $res = $this->productsModel->insertProduct($itemName, $itemPrice, $itemDesc, $itemCat);

        if ($res) {
            $resData['success'] = 1;
            $resData['message'] = 'Изменения успешно внесены';
        } else {
            $resData['success'] = 0;
            $resData['message'] = 'Ошибка изменения данных';
        }

        echo json_encode($resData);
        return;
    }

    /**
     *  Изменить продукт
     * @echo json массив о результате действия ( успешно или ошибка )
     */
    public function updateproductAction() : void
    {
        $itemId = $_POST['itemId'];
        $itemName = $_POST['itemName'];
        $itemPrice = $_POST['itemPrice'];
        $itemStatus = $_POST['itemStatus'];
        $itemDesc = $_POST['itemDesc'];
        $itemCat = $_POST['itemCatId'];

        $res = $this->productsModel->updateProduct($itemId, $itemName, $itemPrice, $itemStatus, $itemDesc, $itemCat);

        if ($res) {
            $resData['success'] = 1;
            $resData['message'] = 'Изменения успешно внесены';
        } else {
            $resData['success'] = 0;
            $resData['message'] = 'Ошибка изменения данных';
        }

        echo json_encode($resData);
        return;
    }

    public function uploadAction() : void
    {
        $maxsize = 2 * 1024 * 1024; // два мегабайта

        $itemId = $_POST['itemId'];
        // получаем расширение загруженного файла
        $ext = pathinfo($_FILES['filename']['name'], PATHINFO_EXTENSION);
        // создаем имя файла
        $newFileName = $itemId . '.' . $ext;
        if ($_FILES['filename']['size'] > $maxsize) {
            echo("Размер файла превышает два мегабайта");
            return;
        }

        // загружен ли файл
        if (is_uploaded_file($_FILES['filename']['tmp_name'])) {
            // если файл загружен то перемещаем его из временной директории в конечную
            $res = move_uploaded_file($_FILES['filename']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . '/images/products/' . $newFileName);
            if ($res) {
                $res = $this->productsModel->updateProductImage($itemId, $newFileName); // обновление страницы
                if ($res) {
                    redirect('/admin/products/');
                }
            }
        } else {
            echo("Ошибка загрузки файла");
        }
    }

    /**
     * Страница заказов
     * @param $this->smarty
     */
    public function ordersAction() : void
    {
        /*
         * в массив добавляются данные с ( orders join users ), паралельно в цикле к каждому ордеру
         * добавляется ['children'] ключ ( массив ), в котором находятся данные о товаре к данному ордеру
         */
        $rsOrders = $this->ordersModel->getOrders();
        $this->smarty->assign('rsOrders', $rsOrders);
        $this->smarty->assign('pageTitle', 'Заказы');

        loadTemplate($this->smarty, 'adminOrders');
    }

    /**
     *  изменение статуса заказа
     * @echo ошибка установки статуса или успех
     */
    public function setorderstatusAction() : void
    {
        $itemId = $_POST['itemId'];
        $status = $_POST['status'];

        $res = $this->ordersModel->updateOrderStatus($itemId, $status);

        if ($res) {
            $resData['success'] = 1;
        } else {
            $resData['success'] = 0;
            $resData['message'] = 'Ошибка установки статуса';
        }

        echo json_encode($resData);
        return;
    }

    /**
     *  установки даты платежа по заказу
     * @echo ошибка установки даты или успех
     */
    public function setorderdatepaymentAction() : void
    {
        $itemId = $_POST['itemId'];
        $datePayment = $_POST['datePayment'];

        $res = $this->ordersModel->updateOrderDatePayment($itemId, $datePayment);

        if ($res) {
            $resData['success'] = 1;
        } else {
            $resData['success'] = 0;
            $resData['message'] = 'Ошибка установки статуса';
        }

        echo json_encode($resData);
        return;
    }

    /**
     *  xml продуктов имеющихся в базе
     */
    public function createxmlAction() : void // test func
    {
        $rsProducts = $this->productsModel->getProducts();

        $xml = new DOMDocument('1.0', 'utf-8');
        $xmpProducts = $xml->appendChild($xml->createElement('products'));
// appendChild - добавить элемент, так как в экземпляре объекта щас было пусто, то можно считать что `products` это
        // корень нашей структуры xml
        foreach ($rsProducts as $product) {
            $xmpProduct = $xmpProducts->appendChild($xml->createElement('product'));
            foreach ($product as $key => $val) {
                $xmlName = $xmpProduct->appendChild($xml->createElement($key));
                $xmlName->appendChild($xml->createTextNode($val));
            }
        }
        $xml->save($_SERVER['DOCUMENT_ROOT'] . '/xml/products/xml');
        echo 'ok';
    }

    /**
     *  функция для отрисовки страницы логина
     */
    public function loginAction() : void
    {

        loadTemplate($this->smarty, 'adminAuth');

    }

    /**
     * принимаем значения для логина админки и логиним
     */
    public function authAction() : void
    {

        $name = $_REQUEST['name'] ?? null;
        $name = trim($name);

        $password = $_REQUEST['password'] ?? null;


        $adminData = $this->adminModel->loginAdmin($name, $password);

        if ($adminData['success']) {

            // $resData - нужно для ajax
            $adminData = $adminData[0]; // чтобы удобно обращаться было

            $_SESSION['admin'] = $adminData; // данные о пользователе в сессию


            $resData = $_SESSION['admin'];
            $resData['success'] = 1;
            //redirect('/admin/');

        } else {
            $resData['success'] = 0;
            $resData['message'] = 'Неверный логин или пароль';

            //redirect('/admin/login/');
        }

        echo json_encode($resData);

    }

    public function logoutAction() : void
    {
        if ($_SESSION['admin']) {
            unset($_SESSION['admin']);
            redirect();
        }
    }

}
