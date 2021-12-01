<?php

/*
 * модель админа
 */

include_once "Model.php";

class AdminModel extends AbstractModel
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

    function loginAdmin($name, $password): bool|array
    {
        $sql = "SELECT * 
            FROM `admins`
            WHERE (`name` = '{$name}' and `pwd` = '{$password}')
            LIMIT 1"; // todo возможно убрать лимит как нибудь


        $res = $this->db->query($sql);
        $resData = createSmartyRsArray($res);

        if ($resData[0]) {

            $resData['success'] = 1;
        } else {
            $resData['success'] = 0;
            $resData['message'] = 'Произошла ошибка при попытке логина';
        }

        return $resData;
    }

}