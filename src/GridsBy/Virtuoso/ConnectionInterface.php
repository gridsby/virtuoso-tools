<?php
namespace GridsBy\Virtuoso;


interface ConnectionInterface
{
    public function fetchAssoc($query);
    public function fetchOne($query);
    public function fetchColumn($query);
    public function exec($query);
}
