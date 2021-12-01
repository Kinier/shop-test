<?php

/**
 * модель для таблицы пользователей ( uesrs )
 */
include_once "Model.php";
class UsersModel extends AbstractModel
{
    /**
     * объект PDO, готовый для вызова методов типа ->query, ->prepare и т.д.
     * @var PDO
     */
    protected PDO $db;
    private orderModel $orderModel;

    public function __construct($databaseObject)
    {
        $this->db = $databaseObject->connect(); // соединение устанавливаем
        $this->orderModel = new orderModel($databaseObject); // TODO important: а вот хз че тут делать, вынести функу куда-то?
    }

    /**
     * регистрация нового пользователя
     *
     * @param string $email почта пользователя
     * @param string $pwdMD5 зашифрованный пароль
     * @param string $name имя пользователя
     * @param string $phone телефон пользователя
     * @param string $address адрес пользователя
     * @return array массив данных нового пользователя ( и статус success = 0 или 1 )
     */
    public function registerNewUser(string $email, string $pwdMD5, string $name, string $phone,string $address): array
    {

        $email = htmlspecialchars($this->db->quote($email));
        $name = htmlspecialchars($this->db->quote($name));
        $phone = htmlspecialchars($this->db->quote($phone));
        $address = htmlspecialchars($this->db->quote($address));

        $sql = "INSERT INTO 
            users (`email`, `pwd`, `name`, `phone`, `address`)
            VALUES ({$email},'{$pwdMD5}',{$name},{$phone},{$address})";

        $rs = $this->db->query($sql);

        /**
         * проверка успешно ли прошла регистрация
         */
        if ($rs) {
            $sql = "SELECT * FROM users
                WHERE (`email` = {$email} and `pwd` = '{$pwdMD5}')
                LIMIT 1";

            $rs = $this->db->query($sql);
            $rs = createSmartyRsArray($rs);


            if (isset($rs[0])) {         // есть ли данные о пользователе
                $rs['success'] = 1;
            } else {
                $rs['success'] = 0;
            }

        }
        return $rs;
    }


    /**
     * Проверка параметров регистрации пользователя
     *
     * @param string $email
     * @param string $pwd1 пароль
     * @param string $pwd2 повтор пароль
     * @return array|null результат
     */
    public function checkRegisterParams(string $email, string $pwd1, string $pwd2): ?array
    {
        $res = null;


        if (!$email) {
            $res ['success'] = false;
            $res ['message'] = 'введите email';
        }

        if (!$pwd1) {
            $res ['success'] = false;
            $res ['message'] = 'введите пароль';
        }

        if (!$pwd2) {
            $res ['success'] = false;
            $res ['message'] = 'введите повторно пароль';
        }


        if ($pwd1 != $pwd2) {
            $res ['success'] = false;
            $res ['message'] = 'Пароли не совпадают';
        }

        return $res;
    }

    /**
     * проверка есть ли такой адрес почты в БД
     *
     * @param string $email
     * @return array массив - строка из таблицы `users`, либо пустой массив
     */
    public function checkUserEmail(string $email): array
    {


        $email = $this->db->quote($email);
        $sql = "SELECT id
            FROM `users`
            WHERE email = {$email}";


        $rs = $this->db->query($sql);
        return createSmartyRsArray($rs);
    }


    /**
     * Авторизация пользователя
     *
     * @param string $email почта ( логин )
     * @param string $pwd пароль
     * @return array массив данных пользователя
     */
    public function loginUser(string $email, string $pwd): array
    {


        $email = $this->db->quote($email);
        $pwd = md5($pwd);

        $sql = "SELECT * 
            FROM users
            WHERE (`email` = {$email} and `pwd` = '{$pwd}')
            LIMIT 1";

        $rs = $this->db->query($sql);

        $rs = createSmartyRsArray($rs);
        if (isset($rs[0])) {
            $rs['success'] = 1;
        } else {
            $rs['success'] = 0;
        }

        return $rs;
    }

    /**
     * updateUserData
     *
     * @param string $name имя пользователя
     * @param string $phone телефон
     * @param string $address адрес
     * @param string $pwd1 новый пароль
     * @param string $pwd2 повтор нового пароля
     * @param string $curPwd текущий пароль
     * @return bool TRUE в случае успеха
     */
    public function updateUserData(string $name, string $phone, string $address, string $pwd1, string $pwd2, string $curPwd): bool
    {



        $email = htmlspecialchars($this->db->quote($_SESSION['user']['email']));
        $name = htmlspecialchars($this->db->quote($name));
        $phone = htmlspecialchars($this->db->quote($phone));
        $address = htmlspecialchars($this->db->quote($address));


        $pwd1 = trim($pwd1);
        $pwd2 = trim($pwd2);

        $newPwd = null;
        // если новый пароль есть и он равен повторно введденному новому паролю
        if ($pwd1 && ($pwd1 == $pwd2)) {
            $newPwd = md5($pwd1);
        }

        $sql = "UPDATE users
            SET";

        if ($newPwd) {
            $sql .= " `pwd` = '{$newPwd}', ";
        }

        $sql .= " `name` = {$name},
              `phone` = {$phone},
              `address` = {$address}
            WHERE
              `email` = {$email} AND `pwd` = '{$curPwd}'
            LIMIT 1";

        $rs = $this->db->query($sql); // здесь же объект - почему не фетчим TODO:?

        return $rs;
    }


    /**
     * Получить данные заказа текущего пользователя
     *
     * ДОП ДАННЫЕ О ФУНКЦИИ НА МЕСТЕ ЕЕ ИСПОЛЬЗОВАНИЯ
     * @return array массив заказов с привязкой к продуктам
     */
    public function getCurUserOrder(): array
    {
        $userId = $_SESSION['user']['id'] ?? 0;
        return $this->orderModel->getOrdersWithProductsByUser($userId);
    }
}