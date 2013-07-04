<?php
namespace GridsBy\Virtuoso;

/**
 * Based on http://docs.openlinksw.com/virtuoso/functions.html#admin
 * @package GridsBy\Virtuoso
 */
class Administration
{
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Returns current working dir of server
     * @return string
     */
    public function serverRoot()
    {
        return $this->connection->fetchOne('select server_root()');
    }

    /**
     * Returns full path to ini-file
     * @return string
     */
    public function iniFilePath()
    {
        $relative_path = $this->connection->fetchOne('select virtuoso_ini_path()');

        if ($relative_path[0] == '.') {
            $path = realpath($this->serverRoot().'/'.$relative_path);
        } else {
            $path = realpath($relative_path);
        }

        return $path;
    }

    /**
     * List of sections in configuration file
     * @return array
     */
    public function configSections()
    {
        $count = $this->connection->fetchOne('select cfg_section_count(virtuoso_ini_path())');

        $sections = [];

        for ($i = 0; $i < $count; $i++) {
            $section_name = $this->connection->fetchOne("select cfg_section_name(virtuoso_ini_path(), {$i})");

            if ($section_name === 0) {
                continue; // bogus section
            }

            $sections[] = $section_name;
        }

        return $sections;
    }

    /**
     * List of parameter-names in specified section of configuration file
     * @param string $section
     * @return array
     */
    public function configSectionParameters($section)
    {
        $count = $this->connection->fetchOne("select cfg_item_count(virtuoso_ini_path(), '{$section}')");

        $parameters = [];

        for ($i = 0; $i < $count; $i++) {
            $parameter_name = $this->connection->fetchOne("select cfg_item_name(virtuoso_ini_path(), {$i})");

            if ($parameter_name == 0) {
                continue; // bogus section
            }

            $parameters[] = $parameter_name;
        }

        return $parameters;
    }

    /**
     * Value of parameter in section of configuration file
     * @param string $section
     * @param string $parameter
     * @return string
     */
    public function configGetValue($section, $parameter)
    {
        return $this->connection->fetchOne("select cfg_item_value(virtuoso_ini_path(), '{$section}', '{$parameter}')");
    }

    /**
     * Sets new value of parameter in section of configuration file
     * NB: new settings won't take effect until server restart
     * @param string $section
     * @param string $parameter
     * @param string $value
     */
    public function configSetValue($section, $parameter, $value)
    {
        $this->connection->exec("cfg_write(virtuoso_ini_path(), '{$section}', '{$parameter}', '{$value}')");
    }

    /**
     * Returns list of directories, which server is allowed to read files from
     * @return array
     */
    public function dirsAllowed()
    {
        $dirs = [];

        $str_value = $this->configGetValue('Parameters', 'DirsAllowed');
        foreach (explode(',', $str_value) as $piece) {
            $piece = trim($piece);

            if ($piece[0] == '.') {
                $piece = $this->serverRoot().'/'.$piece;
            }

            $dirs[] = realpath($piece);
        }

        return $dirs;
    }

    /**
     * Checkpoint should happen once in $minutes
     * By setting checkpoint interval to 0, the checkpoint will only be performed after roll forward upon database startup.
     * A setting of -1 will disable all checkpointing.
     * see http://docs.openlinksw.com/virtuoso/fn_checkpoint_interval.html
     * @param int $minutes
     * @return int
     * @throws \InvalidArgumentException
     */
    public function setCheckpointInterval($minutes)
    {
        if (!is_integer($minutes)) {
            throw new \InvalidArgumentException();
        }

        return intval($this->connection->fetchOne("select checkpoint_interval({$minutes})"));
    }

    /**
     * This function reads through the specified tables and indices and finds groups of adjacent pages holding
     * data that will fit on fewer pages than it currently occupies. If such a compression can be made, the pages
     * are thus compacted.
     * see http://docs.openlinksw.com/virtuoso/fn_vacuum.html
     * @param null|string $table a LIKE pattern for tables to vacuum
     * @param null|string $index a LIKE pattern and if given should match the case and spelling of index names
     */
    public function vacuum($table = null, $index = null)
    {
        if (is_null($table)) {
            $this->connection->exec("DB.DBA.vacuum()");
        } elseif (is_null($index)) {
            $this->connection->exec("DB.DBA.vacuum('{$table}')");
        } else {
            $this->connection->exec("DB.DBA.vacuum('{$table}', '{$index}')");
        }
    }

    /**
     * Create new user
     * @param string $login
     * @param string $password
     */
    public function createUser($login, $password)
    {
        $this->connection->exec("USER_CREATE('{$login}', '{$password}')");
    }

    /**
     * Drop user and (optionally) her resources
     * @param string $login
     * @param bool $cascade
     */
    public function dropUser($login, $cascade = false)
    {
        $cascade = $cascade ? '1' : '0';
        $this->connection->exec("USER_DROP('{$login}', {$cascade})");
    }

    /**
     * Returns statistics for a running server
     * see http://docs.openlinksw.com/virtuoso/fn_status.html
     * @param null|string $option null, 'c', 'k', 'r' or 'h'
     * @return string
     */
    public function status($option = null)
    {
        $query = is_string($option) ? "status('{$option}')" : 'status()';
        $rows = $this->connection->fetchColumn($query);

        return implode("\n", $rows);
    }
}