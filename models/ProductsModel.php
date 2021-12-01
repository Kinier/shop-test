<?php

include_once 'Model.php';

/**
 * модель для таблицы продукции
 */
class ProductsModel extends AbstractModel
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

    function getLastProducts__($limit = null): bool|array
    {

        $sql = "SELECT *
            FROM `products`
            WHERE `status` = 1
            ORDER BY id DESC";

        if ($limit)
            $sql .= " LIMIT $limit";


        $rs = $this->db->query($sql); // rs = record set

        return createSmartyRsArray($rs);

    }

    function getLastProducts($offset = 1, $limit = 9): array
    {

        $sqlCnt = "SELECT count(id) as cnt
                FROM `products`
                WHERE `status` = 1"; // count(id) подсчитывает количество айди


        $rs = $this->db->query($sqlCnt);
        $cnt = createSmartyRsArray($rs);//$db->fetch($rs);

        $sql = "SELECT *
            FROM `products`
            ORDER BY id DESC";
        if ($limit) {
            $sql .= " LIMIT {$offset}, {$limit}"; // если оффсет == 5, выбирутся записи начиная с 6 !!!
        }
        $rs = $this->db->query($sql);
        $rows = createSmartyRsArray($rs);

        return array($rows, $cnt[0]['cnt']); // todo убрать нулевой индекс на 33 строке
    }


    function getProductsByCat($catId): bool|array
    {
        $catId = intval($catId);
        $sql = "SELECT *
             FROM `products`
             WHERE category_id = '{$catId}'";

        $rs = $this->db->query($sql);

        return createSmartyRsArray($rs);

    }

    /**
     * получаем данные продукта по id
     *
     * @param integer $itemId id продукта
     * @return array массив данных продукта
     */
    function getProductById(int $itemId): array
    {
        $itemId = intval($itemId);

        $sql = "SELECT *
            FROM `products`
            WHERE id = '{$itemId}'";

        $rs = $this->db->query($sql);
        return $rs->fetch();    // здесь не смарти аррэй потому что получаем всего один массив
    }


    /**
     * Получить список продуктов из массива идентификаторов
     *
     * @param array $itemsIds массив идентификаторов продуктов
     * @return array массив данных продуктов
     */
    function getProductsFromArray(array $itemsIds): array
    {

        $strIds = implode(', ', $itemsIds);

        $sql = "SELECT *
            FROM `products`
            WHERE `id` in ($strIds)";
        $rs = $this->db->query($sql);

        return createSmartyRsArray($rs);

    }

    function getProducts(): bool|PDOStatement
    {
        $sql = "SELECT *
            FROM `products`
            ORDER BY category_id";


        return $this->db->query($sql);
    }

    /**
     * Добавление нового товара
     *
     * @param string $itemName Название продукта
     * @param integer $itemPrice Цена
     * @param string $itemDesc Описание
     * @param integer $itemCat ID категории
     * @return false|PDOStatement
     */
    function insertProduct(string $itemName, int $itemPrice, string $itemDesc, int $itemCat): bool|PDOStatement
    {
        $sql = "INSERT INTO `products`
            SET
                `name` = '{$itemName}',
                `price` = '{$itemPrice}',
                `description` = '{$itemDesc}',
                `category_id` = '{$itemCat}'";



        return $this->db->query($sql);
    }

    function updateProduct($itemId, $itemName, $itemPrice, $itemStatus, $itemDesc, $itemCat, $newFileName = null): bool|PDOStatement
    {
        $set = array();

        if ($itemName) {
            $set[] = "`name` = '{$itemName}'";
        }

        if ($itemPrice > 0) {
            $set[] = "`price` = '{$itemPrice}'";
        }

        if ($itemStatus !== null) {
            $set[] = "`status` = '{$itemStatus}'"; // если галка стоит, то status = 0 ( т.е. товар как бы спрятан )
        }

        if ($itemDesc) {
            $set[] = "`description` = '{$itemDesc}'";
        }

        if ($itemCat) {
            $set[] = "`category_id` = '{$itemCat}'";
        }

        if ($newFileName) {
            $set[] = "`image` = '{$newFileName}'";
        }

        $setStr = implode(", ", $set);
        $sql = "UPDATE `products`
            SET {$setStr}
            WHERE id ='{$itemId}'";



        return $this->db->query($sql);
    }

    function updateProductImage($itemId, $newFileName): bool|PDOStatement
    {
        return $this->updateProduct($itemId, null, null, null, null, null, $newFileName); // fixme
    }
}
