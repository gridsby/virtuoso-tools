<?php
namespace GridsBy\Virtuoso;


class Connection
{
    private $pdo;

    public function __construct($odbc_name = 'Local Virtuoso u', $login = 'dba', $password = 'dba')
    {
        $this->pdo = new \PDO('odbc:Local Virtuoso u', 'dba', 'dba'/*, array(\PDO::ODBC_ATTR_ASSUME_UTF8 => true)*/);
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }

    public function fetchAssoc($query)
    {
        $q = $this->pdo->prepare($query);
        $q->execute();

        return $q->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function fetchOne($query)
    {
        $q = $this->pdo->prepare($query);
        $q->execute();

        $return_value = $q->fetchColumn(0);
        $q->closeCursor();

        return $return_value;
    }

    public function fetchColumn($query)
    {
        $q = $this->pdo->prepare($query);
        $q->execute();

        return $q->fetchAll(\PDO::FETCH_COLUMN, 0);
    }

    public function exec($query)
    {
        return $this->pdo->exec($query);
    }
}
