<?php
namespace jeyroik\components\repositories;

use jeyroik\interfaces\repositories\IRepository;
use jeyroik\interfaces\repositories\IRepositoryFactory;

class RepositoryFactory implements IRepositoryFactory
{
    protected static array $repos = [
        // item class => table object
    ];

    public static function get(string $class, $dbClass = '', $dbName = ''): IRepository
    {
        if (!isset(self::$repos[$class])) {
            $parts = explode('\\', $class);
            $tableName = strtolower(array_pop($parts));
            $dbClass = empty($dbClass) ? DB__CLASS : $dbClass;
            self::$repos[$class] = new $dbClass(empty($dbName) ? DB__NAME : $dbName, $tableName, $class);
        }

        return self::$repos[$class];
    }
}
