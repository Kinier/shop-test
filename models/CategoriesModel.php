<?php

/**
 * модель таблицы категорий
 */

include_once 'Model.php';

class CategoriesModel extends AbstractModel
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
     * Получить дочерние категории для категории $catId
     * @param integer $catId ID основной категории
     * @return array массив дочерних категорий
     */
    public function getChildrenForCat(int $catId): array
    {
        $sql = "SELECT *
     FROM `categories`
     WHERE parent_id='{$catId}'";

        $rs = $this->db->query($sql);

        return createSmartyRsArray($rs); // обработаем запрос и получим все категории родительского айди
    }




    function getCatById($catId)
    {
        $catId = intval($catId);
        $sql = "SELECT *
            FROM `categories`
            WHERE id='{$catId}'";

        //$db = getAndCreateDbConnection();
        $rs = $this->db->query($sql);

        return $rs->fetch();
    }

    /**
     * Получить все главные категории
     * @return array|false
     */
    function getAllMainCategories(): bool|array
    {
        $sql = "SELECT *
             FROM `categories`
             WHERE parent_id = 0";

        //$db = getAndCreateDbConnection();
        $rs = $this->db->query($sql);

        return createSmartyRsArray($rs);
    }

    /**
     * Получить главные категории с привязками дочерних
     *
     * @return array массив категорий
     */
    public function getAllMainCatsAndChildren(): array
    {

        $sql = 'SELECT *
            FROM `categories`
            WHERE parent_id=0'; // основные категории
        //$db = getAndCreateDbConnection();


        $rs = $this->db->query($sql); // rs = record set // основные категории
        $smartyRS = array();
        while ($row = $rs->fetch()) {
            $rsChildren = $this->getChildrenForCat($row['id']);    // получаем  все дочерние категории по основной

            if ($rsChildren) {
                $row['children'] = $rsChildren;
            }

            $smartyRS[] = $row;

        }
        return $smartyRS;
    }

    /**
     * Добавление новой категории
     * @param string $catName Название категории
     * @param int $catParentId ID родительской категории
     * @return int id новой категории
     */
    function insertCat(string $catName, int $catParentId = 0): int
    {
        // готовим запрос
        $sql = "INSERT INTO
            categories (`parent_id`, `name`)
            VALUE ('{$catParentId}', '{$catName}')";

        //выполняем запрос
        //$db = getAndCreateDbConnection();
        //$rs = $db->query($sql);

        // получаем id добавленной записи
        return $this->db->lastInsertId();
    }

    /**
     * Получить все категории
     * @return array|false
     */
    function getAllCategories(): bool|array
    {
        $sql = 'SELECT *
            FROM `categories`
            ORDER BY parent_id ASC';

        //$db = getAndCreateDbConnection();
        $rs = $this->db->query($sql);

        return createSmartyRsArray($rs);
    }

    /**
     * Обновление категорий
     * @param integer $itemId ID категории
     * @param int $parentId ID главной категории
     * @param string $newName новое имя категории
     */
    function updateCategoryData(int $itemId, int $parentId = -1, string $newName = ''): bool|PDOStatement
    {
        $set = array();


        /*
         * если не пришло новое имя или парент айди, то мы их просто не меняем
         */
        if ($newName) {
            $set[] = "`name` = '{$newName}'";
        }

        if ($parentId > -1) {
            $set[] = "`parent_id` = '{$parentId}'";
        }

        $setStr = implode(", ", $set);
        $sql = "UPDATE categories
            SET {$setStr}
            WHERE id = '{$itemId}'";

        //$db = getAndCreateDbConnection();
        return $this->db->query($sql);
    }
}