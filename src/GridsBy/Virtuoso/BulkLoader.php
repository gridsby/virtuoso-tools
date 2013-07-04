<?php
namespace GridsBy\Virtuoso;

/**
 * Class BulkLoader
 *
 * Based on guide at http://virtuoso.openlinksw.com/dataspace/doc/dav/wiki/Main/VirtBulkRDFLoader
 * and sources at https://github.com/openlink/virtuoso-opensource/blob/develop/7/libsrc/Wi/rdflddir2.sql
 *
 * @package GridsBy\Virtuoso
 */
class BulkLoader
{
    const LOG_TERMINATE_DML_LOGGING = 0b0;
    const LOG_RESUME_DML_LOGGING = 0b1;
    const LOG_AUTOCOMMIT = 0b10;

    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function listTasks()
    {
        return $this->connection->fetchAssoc('SELECT * FROM DB.DBA.load_list');
    }

    public function cleanTasks()
    {
        $this->connection->exec("DELETE FROM DB.DBA.load_list");
    }

    public function addTask($path, $mask, $graph)
    {
        try {
            $this->connection->exec("ld_dir('{$path}', '{$mask}', '{$graph}')");
        } catch (\PDOException $e) {
            if ($e->getCode() == 42000 and strpos($e->getMessage(), 'denied due to access control in ini file') !== false) {
                throw new DirIsNotAllowedException("Directory {$path} is not included into DirsAllowed parameter of your virtuoso.ini", 0, $e);
            }

            throw $e;
        }
    }

    public function addRecursiveTask($path, $mask, $graph)
    {
        try {
            $this->connection->exec("ld_dir_all('{$path}', '{$mask}', '{$graph}')");
        } catch (\PDOException $e) {
            if ($e->getCode() == 42000 and strpos($e->getMessage(), 'denied due to access control in ini file') !== false) {
                throw new DirIsNotAllowedException("Directory {$path} is not included into DirsAllowed parameter of your virtuoso.ini", 0, $e);
            }

            throw $e;
        }
    }

    /**
     * Starts a import-thread. It will "hang" until all files are imported
     * @param null|integer $max_files  maximum number of files which should be processed in this thread
     * @param null|integer $log_enable bitmask of BulkLoader::LOG_* constants
     */
    public function runTasks($max_files = null, $log_enable = null)
    {
        $max_files = is_null($max_files) ? 'null' : strval($max_files);

        if (is_null($log_enable)) {
            $log_enable = BulkLoader::LOG_TERMINATE_DML_LOGGING | BulkLoader::LOG_AUTOCOMMIT;
        }

        $log_enable = strval($log_enable);

        $this->connection->exec("rdf_loader_run({$max_files}, {$log_enable})");
    }

    /**
     * Runs single file-load task from the queue
     * @param null|integer $log_enable bitmask of BulkLoader::LOG_* constants
     */
    public function runOneTask($log_enable = null)
    {
        $this->runTasks(1, $log_enable);
    }

    /**
     * Flips up "stop" flag, which tells all running import-threads to stop after they finish current task
     */
    public function gracefullyStopTasks()
    {
        $this->connection->exec("rdf_load_stop(0)");
    }
}