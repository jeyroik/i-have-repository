<?php
namespace jeyroik\interfaces\repositories\plugins;

use jeyroik\interfaces\IHaveAttributes;

interface IRepositoryPlugin extends IHaveAttributes
{
    public function __invoke(string $method, array $item): array;
}
