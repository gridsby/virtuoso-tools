<?php
namespace GridsBy\Virtuoso;


class PDOConnection implements ConnectionInterface
{
    private $pdo;

    public function __construct($host='localhost', $port=1111, $login='dba', $password='dba')
    {
        $driver = 'OpenLink Virtuoso ODBC Driver (Unicode)';

        $this->pdo = new \PDO("odbc:DRIVER={$driver};HOSTNAME={$host};PORT={$port}", $login, $password, [\PDO::ODBC_ATTR_ASSUME_UTF8 => true]);
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
