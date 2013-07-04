<?php
/**
 * Created by IntelliJ IDEA.
 * User: indy
 * Date: 04.07.13
 * Time: 23:51
 * To change this template use File | Settings | File Templates.
 */

namespace GridsBy\Virtuoso;


class TransactionManager
{
    const ERR_TIMEOUT = 1;
    const ERR_ROLLBACK_AFTER_SQL_ERROR = 6;

    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Kills all active transactions
     * @param int $status transactions will return this code to their callers
     */
    public function killAll($status = self::ERR_ROLLBACK_AFTER_SQL_ERROR)
    {
        $this->connection->exec("txn_killall({$status})");
    }
}
