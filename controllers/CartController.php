<?php


/** CartController.php
 *  для работы с корзиной /cart/
 */

 // подключаем модели

include_once "../models/CategoriesModel.php";
include_once "../models/ProductsModel.php";
include_once "../models/PurchaseModel.php";
include_once "../models/OrderModel.php";

include_once "Controller.php";

class CartController extends AbstractController
{
    protected object $databaseObject; // объект класса базы данных
    protected object $categoriesModel; // модель
    protected object $smarty;
    protected object $productsModel;
    protected object $orderModel;
    protected object $purchaseModel;
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
        $this->orderModel = new OrderModel($databaseObject);
        $this->purchaseModel = new purchaseModel($databaseObject);
    }

    /**
     * Добавиь в корзину
     *
     *
     *
     * @GET int 'id' GET параметр - ид добавляемого продукта
     * @return bool информация об операции (успех, колво элементов в корзине)
     */
    public function addtocartAction(): bool
    {
        $itemId = isset($_GET['id']) ? intval($_GET['id']) : null;

        if (!$itemId) return false;

        $resData = array();

        // елси значение не найдено то добавляем

        if (isset($_SESSION['cart']) && array_search($itemId, $_SESSION['cart']) === false) {


            $_SESSION['cart'][] = $itemId;


            $resData['cntItems'] = count($_SESSION['cart']);
            $resData['success'] = 1;
        } else {
            $resData['success'] = 0;
        }


        echo json_encode($resData);
        return true; // чисто для вида. Раньше вообще ничего не возвращало, поставил из-за тайп хинтинга
    }

    /**
     * Удаление продукта из корзины
     *
     * @GET int 'id' GET параметр - ID удаляемого из корзины продукта
     * @echo json информация об операции ( успех, кол-во элементов в корзине )
     */
    public function removefromcartAction(): void
    {
        $itemId = isset($_GET['id']) ? intval($_GET['id']) : null;
        if (!$itemId) exit();

        $resData = array();
        $key = array_search($itemId, $_SESSION['cart']);
        if ($key !== false) {
            unset($_SESSION['cart'][$key]);
            $resData['success'] = 1;
            $resData['cntItems'] = count($_SESSION['cart']);
        } else {
            $resData['success'] = 0;
        }

        echo json_encode($resData);
    }

    /**
     * Формирование страницы корзины
     * @link /cart/
     */

    public function indexAction()
    {
        $itemsIds = $_SESSION['cart'] ?? array();

        $rsCategories = $this->categoriesModel->getAllMainCatsAndChildren(); // для отрисовки левого меню
        if (!empty($itemsIds))
            $rsProducts = $this->productsModel->getProductsFromArray($itemsIds);
        else
            $rsProducts = null;


        $this->smarty->assign('pageTitle', 'Корзина');
        $this->smarty->assign('rsCategories', $rsCategories);
        $this->smarty->assign('rsProducts', $rsProducts);
        $this->smarty->assign('rsCategory', null);

        loadTemplate($this->smarty, 'cart');
    }

    /**
     * формирование страницы заказа
     * данные с формы приходят только если это тег <input>, и имеются атрибуты name и value
     */
    public function orderAction($smarty): void
    {
        // получаем массив иденитфикаторов (id) продуктов корзины
        $itemsIds = $_SESSION['cart'] ?? null;
        // если корзина пуста то пернаправляем в корзину
        //>
        if (!$itemsIds) {
            redirect('/cart/');
        }
        //< если массив 'cart' полностью пустой, то произойдет перенаправление

        // получаем из массива  $_POST количество покупаемых товаров
        $itemsCnt = array();
        foreach ($itemsIds as $item) { // из сессии берем айдишники товаров в корзине
            // формируем ключ для массива $_POST
            $postVar = 'itemCnt_' . $item;                                           // TODO: можно не трогать сессию и просто $_POST отформатировать ( хз как, может и не можно :) )
            // создаем элемент массива количествоа покупаемого товара
            // ключ массива - id товара, значение массива - количество товара
            // $itemsCnt[1] = 3, товара с id = 1 покупают 3 штуки
            $itemsCnt[$item] = $_POST[$postVar] ?? null;
        }

        // получаем список продуктов по массиву корзины
        $rsProducts = $this->productsModel->getProductsFromArray($itemsIds);

        // добавляем каждому продукту дополнительное поле
        // "realPrice = количество продуктов * на цену продукта"
        // "cnt" = количество покупаемого товара

        //$&item - для того чтобы при изменении переменной $item
        // менялся и элемент массива $rsProducts

        $i = 0; // TODO: переработать цикл, так как хуево сделано, можно попробовать без ссылки "as $key => $item"
        foreach ($rsProducts as &$item) {
            $item['cnt'] = $itemsCnt[$item['id']] ?? 0; // добавляем в массив индекс cnt( количество )
            if ($item['cnt']) { // больше 0
                $item['realPrice'] = $item['cnt'] * $item['price']; // цену за один товар умножаем на количество этого товара
            } else {
                // если вдруг получилось так что товар в корзине есть, а количество == нулю,
                // то удаляем этот товар
                unset($rsProducts[$i]);
            }
            $i++;
        }
        if (!$rsProducts) {
            echo "Корзина пуста";
            return;
        }

        // полученный массив покупаемых товаров помещаем в сессионную переменную
        $_SESSION['saleCart'] = $rsProducts;

        $rsCategories = $this->categoriesModel->getAllMainCatsAndChildren();

        // hideLoginBox переменная - флаг для скрытия блоков логина
        // и регистраци в боковой панели
        if (!isset($_SESSION['user'])) {
            $smarty->assign('hideLoginBox', 1);
        }

        $smarty->assign('pageTitle', 'Заказ');
        $smarty->assign('rsCategories', $rsCategories);
        $smarty->assign('rsProducts', $rsProducts);
        $smarty->assign('rsCategory', null);
        loadTemplate($smarty, 'order');
    }


    /**
     * ajax функция сохранения заказов
     *
     * @SESSION array $_SESSION['saleCart'] массив покупаемых продуктов
     * @echo json информация о результате выполнения
     */
    public function saveorderAction(): void
    {
        // получаем массив покупаемых товаров

        $cart = $_SESSION['saleCart'] ?? null;
        // в 'saleCart' полные данные о желаемых товарах(также кол-во и общая цена)
        // если корзина пуста, то формируем ответ с ошибкой, отдаем его в формате
        // json и выходим из функции

        if (!$cart) {
            $resData['success'] = 0;
            $resData['message'] = 'Нет товаров для заказа';
            echo json_encode($resData);
            return;
        }

        /**
         * если мы логинимся прямо на странице заказа, то имя, телефон и адрес берутся из формы регистрации
         * которая скрыта, но заполнена ( см. функцию login() в файле 'main.js')
         */
        $name = $_POST['name'] ?? null;
        $phone = $_POST['phone'] ?? null; // не создано сообщение об ошибке при отсутствии переменных
        $address = $_POST['address'] ?? null;

        // создаем новый заказ и получаем его id
        $orderId = $this->orderModel->makeNewOrder($name, $phone, $address);
        // если заказ не создан, то выдаем ошибку и завершаем функцию
        if (!$orderId) {
            $resData['success'] = 0;
            $resData['message'] = 'Ошибка создания заказа';
            echo json_encode($resData);
            return;
        }

        $res = $this->purchaseModel->setPurchaseForOrder($orderId, $cart);

        // если успешно, то формируем ответ, удаляем переменные корзины
        if ($res) {
            $resData['success'] = 1;
            $resData['message'] = 'Заказ сохранен';
            unset($_SESSION['saleCart']);
            unset($_SESSION['cart']);
        } else {
            $resData['success'] = 0;
            $resData['message'] = 'Ошибка внесения данных для заказа №';
        }

        echo json_encode($resData);
    }

}