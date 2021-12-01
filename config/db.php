<?php


class DatabaseConnectPDO{
public string $dblocation;
public string $dbname;
public string $dbuser;
public string $dbpassword;
public string $charset;
    public function __construct()
{
    $this->dblocation = 'localhost';
    $this->dbname = 'myshop';
    $this->dbuser = 'root';
    $this->dbpassword = 'pipapipa123';
    $this->charset = 'utf8';
}

    /** подключение к базе данных */
    public function connect(): PDO
    {



        $dsn = "mysql:host=$this->dblocation;dbname=$this->dbname;charset=$this->charset"; // data source name

        $opts = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false];



        return new PDO($dsn, $this->dbuser, $this->dbpassword, $opts);// соединение с бд
    }



}
