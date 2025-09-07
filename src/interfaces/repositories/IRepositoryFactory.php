<?php
namespace jeyroik\interfaces\repositories;

interface IRepositoryFactory
{
    /**
     * If $dbClass is empty, than DB__CLASS env is used.
     * If $dbName is empty, than DB__NAME env is used.
     */
    public static function get(string $class, string $dbClass = '', string $dbName = ''): IRepository;
}
