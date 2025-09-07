<?php
namespace jeyroik\interfaces\repositories;

interface IHaveRepository
{
    public function getRepo(string $itemClass, string $dbClass = '', string $dbName = ''): IRepository;
}
