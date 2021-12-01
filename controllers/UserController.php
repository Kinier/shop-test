<?php

/**
 * Контроллер функций пользователя
 * 
 * 
 */

use JetBrains\PhpStorm\NoReturn;

include_once '../models/CategoriesModel.php';
include_once '../models/UsersModel.php';
include_once '../models/OrderModel.php';
include_once '../models/PurchaseModel.php';
include_once '../models/ProductsModel.php';

include_once "Controller.php";

class UserController extends AbstractController
{
    protected object $databaseObject; // объект класса базы данных
    protected object $categoriesModel; // модель
    protected object $smarty;
    protected object $productsModel;
    protected object $usersModel;
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
        $this->usersModel = new UsersModel($databaseObject);
        $this->purchaseModel = new PurchaseModel($databaseObject);
    }

    /**
     * AJAX регистрация пользователя
     *
     * Инициализация сессионной переменной ( $_SESSION['user'] )
     *
     * @echo object массив данных нового пользователя
     */
    public function registerAction(): void
    {


        $email = $_REQUEST['email'] ?? null;
        $email = trim($email);

        $pwd1 = $_REQUEST['pwd1'] ?? null;
        $pwd2 = $_REQUEST['pwd2'] ?? null;

        $phone = $_REQUEST['phone'] ?? null;
        $address = $_REQUEST['address'] ?? null;
        $name = $_REQUEST['name'] ?? null;
        $name = trim($name);

        //$resData = null;
        $resData = $this->usersModel->checkRegisterParams($email, $pwd1, $pwd2); // null если нет ошибок


        /**
         * возможно стоит эту проверку перенести в функцию checkRegisterParams
         */
        if (!$resData && $this->usersModel->checkUserEmail($email)) {  // если ошибок нет, но мыло уже такое зарегистрировано
            $resData['success'] = false;
            $resData['message'] = "Пользователь с таким email ($email) уже зарегистрирован"; //теперь массив не пустой, следующее условие не пройдет
        }

        if (!$resData) { // если ошибок нет
            $pwdMD5 = md5($pwd1);

            $userData = $this->usersModel->registerNewUser($email, $pwdMD5, $name, $phone, $address);

            if ($userData['success']) {
                $resData['message'] = 'Пользователь успешно зарегистрирован';
                $resData['success'] = 1;
                // $resData - нужно для ajax
                $userData = $userData[0]; // чтобы удобно обращаться было
                $resData['userName'] = $userData['name'] ? $userData['name'] : $userData['email']; // нету имени - добавляем почту
                $resData['userEmail'] = $email;

                $_SESSION['user'] = $userData; // данные о пользователе в сессию
                $_SESSION['user']['displayName'] = $userData['name'] ? $userData['name'] : $userData['email'];
            } else {
                $resData['success'] = 0;
                $resData['message'] = 'Ошибка регистрации';
            }
        }

        echo json_encode($resData);
    }

    /**
     * разлогинивание пользователя
     *
     */

    #[NoReturn] public function logoutAction()
    {
        if (isset($_SESSION['user'])) {
            unset($_SESSION['user']);
            unset($_SESSION['cart']);
        }

        redirect('/');
    }

    /**
     * ajax авторизация пользователя
     * @echo object json массив данных пользователя
     */


    public function loginAction()
    {
        $email = $_REQUEST['email'] ?? null;
        $email = trim($email);

        $pwd = $_REQUEST['pwd'] ?? null;
        $pwd = trim($pwd);


        $userData = $this->usersModel->loginUser($email, $pwd);

        if ($userData['success']) {

            // $resData - нужно для ajax
            $userData = $userData[0]; // чтобы удобно обращаться было

            $_SESSION['user'] = $userData; // данные о пользователе в сессию
            $_SESSION['user']['displayName'] = $userData['name'] ? $userData['name'] : $userData['email']; // показываем мыло если имя не указано

            $resData = $_SESSION['user'];
            $resData['success'] = 1;


        } else {
            $resData['success'] = 0;
            $resData['message'] = 'Неверный логин или пароль';
        }

        echo json_encode($resData);
    }


    /**
     * формирование главной страницы пользователя
     *
     * @link /user/
     */

    public function indexAction()
    {
        // если пользователь не авторизован, перенаправляем на главную страницу
        if (!isset($_SESSION['user'])) {
            redirect('/');
        }

        // получаем список категорий для меню
        $rsCategories = $this->categoriesModel->getAllMainCatsAndChildren();
        // странное решение строчкой сверху и снизу, подробнее в записке "добавить службы"
        // получаем список заказов пользователя

        $rsUserOrders = $this->usersModel->getCurUserOrder();
        //d($rsUserOrders);


        $this->smarty->assign('pageTitle', 'Страница пользователя');
        $this->smarty->assign('rsCategories', $rsCategories);
        $this->smarty->assign('rsUserOrders', $rsUserOrders);
        $this->smarty->assign('rsCategory', null);

        loadTemplate($this->smarty, 'user');
    }

    /**
     * обновление данных пользователя
     *
     * @echo object|none результаты выполнения функции
     * @return false|void
     */

    public function updateAction()
    {
        //> пользователь здесь обязательно должен быть авторизован
        if (!isset($_SESSION['user'])) {
            redirect('/');
        }
        //<

        //> инициализация переменных
        $resData = array();
        $phone = $_REQUEST['phone'] ?? null;
        $address = $_REQUEST['address'] ?? null;
        $name = $_REQUEST['name'] ?? null;
        $pwd1 = $_REQUEST['pwd1'] ?? null;
        $pwd2 = $_REQUEST['pwd2'] ?? null;
        $curPwd = $_REQUEST['curPwd'] ?? null;

        //<

        // проверка правильности введенного пароля ( пароль аккаунта сравнивается с паролем введенным в форму )
        $curPwdMD5 = md5($curPwd);

        if (!$curPwd || ($_SESSION['user']['pwd'] != $curPwdMD5)) {
            $resData['success'] = 0;
            $resData['message'] = 'Текущий пароль не верный';
            echo json_encode($resData);
            return false;
        }


        // обновление данных пользователя

        $res = $this->usersModel->updateUserData($name, $phone, $address, $pwd1, $pwd2, $curPwdMD5); // возвращает false если не получилось

        if ($res) {
            $resData['success'] = 1;
            $resData['message'] = 'Данные сохранены';
            $resData['userName'] = $name;

            $_SESSION['user']['name'] = $name;
            $_SESSION['user']['phone'] = $phone;
            $_SESSION['user']['address'] = $address;

            //$password = $_SESSION['user']['pwd'];
            if ($pwd1 && ($pwd1 == $pwd2)) { // если новый пароль совпадает с новым повторным
                $password = md5(trim($pwd1));
                $_SESSION['user']['pwd'] = $password;
            }
            /* FIXME: переделать блок 5 строк сверху(начиная с этой) Чутка переделал, мб так оставлю 26.09.2021
           тупо сделано, переменная называется $newPwd,
           но если пароль не изменялся, то здесь хранится старый пароль
           */

            $_SESSION['user']['displayName'] = $name ? $name : $_SESSION['user']['email'];
        } else {
            $resData['success'] = 0;
            $resData['message'] = "Ошибка сохранения данных";
        }

        echo json_encode($resData);

    }

}