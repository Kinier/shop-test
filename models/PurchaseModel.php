<?php

/**
 * модель для таблицы продукции (purchase)
 */
include_once "Model.php";


class PurchaseModel extends AbstractModel
{
    /**
     * объект PDO, готовый для вызова методов типа ->query, ->prepare и т.д.
     * @var PDO
     */
    protected PDO $db;

    public function __construct($databaseObject)
    {
        $this->db = $databaseObject->connect(); // соединение устанавливаем
    }

    /**
     * Внесение в БД данных продуктов с привязкой к заказу
     *
     * @param integer $orderId ID заказа
     * @param array $cart массив корзины
     *
     * @return boolean TRUE в случае успешного добавления в БД
     */
    function setPurchaseForOrder(int $orderId, array $cart)
    {
        $sql = "INSERT INTO purchase
            (`order_id`, `product_id`, `price`, `amount`)
            VALUES ";

        $values = array();
        // формируем массив строк для запроса каждого товара
        foreach ($cart as $item) {
            $values[] = "('{$orderId}', '{$item['id']}','{$item['price']}','{$item['cnt']}')";
        }

        // проеобразовываем массив в строку
        $sql .= implode(', ', $values);

        $rs = $this->db->query($sql);

        return $rs;
    }

    function getPurchaseForOrder($orderId): bool|array
    {
        # делаем выборку из таблицы покупок и объдиняем ее с таблицей продуктов с помощью JOIN
        /*
         * SELECT `purchase`.*,`products`.`name` FROM purchase JOIN products ON `purchase`.product_id = `products`.id WHERE `purchase`.order_id = $orderId
         *
         * Выбираем все поля из таблицы покупок(purchase) и поле `name` из таблицы  продуктов (products)
         *
         * ДЖОИНИМ таблицу `products` ( join products) там, где product_id из `purchase` совпадет с id из `products`
         * вот так это выглядит .. JOIN products ON `purchase`.product_id = `products`.id
         *
         * ТОЕСТЬ ПОДКЛЮЧАЕМ ТАБЛИЦУ ПРОДУКТЫ, А ИМЕННО ТЕ ПОЛЯ ГДЕ product_id из `purchase` совпадает с id из `products`
         * В ИТОГЕ ПОЛУЧАЕТСЯ ПРИСОЕДИНЯЕТСЯ ТОЛЬКО ИМЯ, ПОТОМУ ЧТО МЫ ВЫБРАЛИ ТОЛЬКО ЕГО ЕЩЕ НА ПЕРВОЙ СТРОКЕ ЗАПРОСА
         */
        $sql = "SELECT `pe`.*,`ps`.`name`
            FROM purchase as `pe`
            JOIN products as `ps` ON `pe`.product_id = `ps`.id
            WHERE `pe`.order_id = {$orderId}";

        //d($sql);
        $rs = $this->db->query($sql);

        return createSmartyRsArray($rs);
    }
}