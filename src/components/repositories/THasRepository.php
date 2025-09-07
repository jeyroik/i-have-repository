<?php
namespace jeyroik\components\repositories;

use jeyroik\interfaces\repositories\IRepository;

trait THasRepository
{
    public function getRepo(string $itemClass, string $dbClass = '', string $dbName = ''): IRepository
    {
        return RepositoryFactory::get($itemClass, $dbClass, $dbName);
    }
}
