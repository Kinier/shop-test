<?php

/**
 * модель для таблицы заказов
 */
include_once "PurchaseModel.php";

include_once "Model.php";

class OrderModel extends AbstractModel
{
    /**
     * объект PDO, готовый для вызова методов типа ->query, ->prepare и т.д.
     * @var PDO
     */
    protected PDO $db;
    private object $purchaseModel;

    public function __construct($databaseObject)
    {
        $this->db = $databaseObject->connect(); // соединение устанавливаем
        $this->purchaseModel = new purchaseModel($databaseObject); // TODO important: а вот хз че тут делать, вынести функу куда-то?
    }

    /**
     * создание заказа ( без привязки товара )
     *
     * @param string $name
     * @param string $phone
     * @param string $address
     * @return bool|int ID созданного заказа
     */
    public function makeNewOrder(string $name, string $phone, string $address): bool|int
    {
        //> инициализация переменных
        $userId = $_SESSION['user']['id'];
        $comment = "id пользователя: {$userId}<br>
                Имя: {$name}<br>
                Тел: {$phone}<br>
                Адрес: {$address}";

        $dateCreated = date('Y.m.d H:i:s');
        $userIp = $_SERVER['REMOTE_ADDR'];
        //< TODO: грязные переменные, сделать проверки на случаи sql инъекций


        // формирование запроса к БД
        $sql = "INSERT INTO
            orders (`user_id`, `date_created`, `date_payment`,
            `status`, `comment`, `user_ip`)
            VALUES ('{$userId}', '{$dateCreated}', null,
             '0', '{$comment}', '{$userIp}')";

        $rs = $this->db->query($sql);

        // получаем айди созданного заказа
        if ($rs) {
            return $this->db->lastInsertId(); // если не работает смотреть 5.10 время 8:11
        }

        return false;
    }

    /**
     * Получить список заказов с привязкой к продуктам для пoльзователя $userId
     *
     * @param integer $userId ID пользователя
     * @return array массив заказов с привязкой к продуктам
     */
    public function getOrdersWithProductsByUser(int $userId): array
    {
        $userId = intval($userId);
        $sql = "SELECT *
            FROM orders
            WHERE `user_id` = '{$userId}'
            ORDER BY id DESC";

        $rs = $this->db->query($sql);

        $smartyRs = array();//>
        while ($row = $rs->fetch()) { // фетчим в цикле, допустим один ордер щас есть, в итоге по его айди получаем все товары
            $rsChildren = $this->purchaseModel->getPurchaseForOrder($row['id']); // покупки

            if ($rsChildren) {
                $row['children'] = $rsChildren;
                $smartyRs[] = $row;
            }
        }

        return $smartyRs;//<
    }

    public function getOrders(): array
    {
        $sql = "SELECT o.*, u.name, u.email, u.phone, u.address
            FROM `orders` AS `o`
            LEFT JOIN `users` AS `u` ON o.user_id = u.id
            ORDER BY id DESC";

        $rs = $this->db->query($sql);

        $smartyRs = array();
        while ($row = $rs->fetch()) {
            $rsChildren = $this->getProductsForOrder($row['id']); // пихаем айди ордера

            if ($rsChildren) {
                $row['children'] = $rsChildren;
                $smartyRs[] = $row;
            }
        }
        return $smartyRs;
    }

    /**
     * Получить продукты заказа
     * @param int $orderId ID заказа
     * @return array|false массив данных товаров
     */
    public function getProductsForOrder(int $orderId): bool|array
    {
        $sql = "SELECT *
            FROM purchase AS pe
            LEFT JOIN products AS ps
            ON pe.product_id = ps.id
            WHERE (`order_id` = '{$orderId}')";

        $rs = $this->db->query($sql);
        return createSmartyRsArray($rs);
    }

    /**
     * @param $itemId
     * @param $status
     * @return false|PDOStatement
     */
    public function updateOrderStatus($itemId, $status): bool|PDOStatement
    {
        $status = intval($status);
        $sql = "UPDATE orders
            SET `status` = '{$status}'
            WHERE id = '{$itemId}'";

        return $this->db->query($sql);
    }

    /**
     * @param $itemId
     * @param $datePayment
     * @return false|PDOStatement
     */
    public function updateOrderDatePayment($itemId, $datePayment): bool|PDOStatement
    {
        $sql = "UPDATE orders
            SET `date_payment` = '{$datePayment}'
            WHERE id = '{$itemId}'";

        return $this->db->query($sql);
    }
}