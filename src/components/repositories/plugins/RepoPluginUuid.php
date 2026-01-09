<?php
namespace jeyroik\components\repositories\plugins;

use jeyroik\interfaces\attributes\IHaveId;
use jeyroik\components\THasAttributes;
use jeyroik\interfaces\repositories\plugins\IRepositoryPlugin;
use Ramsey\Uuid\Uuid;

/**
 * Effect:
 * Set an uuid to the jeyroik\interfaces\attributes\IHaveId::FIELD__ID field on insert
 * 
 * Usage:
 * 1. Define REPOSITORY__PLUGIN_FILE path.
 * 2. By this path make a file with php script, which returns an array:
 * return [
 *      jeyroik\components\repositories\plugins\RepoPluginUuid::class => [
 *          RepoPluginUuid::OPTION__REWRITE => RepoPluginUuid::REWRITE_ON
 *      ]
 * ];
 */
class RepoPluginUuid implements IRepositoryPlugin
{
    public const OPTION__REWRITE = 'rw';
    public const REWRITE_ON = true;
    public const REWRITE_OFF = false;

    use THasAttributes;

    public function __invoke(string $method, array $item): array
    {
        if (($method === 'insertOne') || ($method === 'insertMany')) {
            return $this->insertOne($item);
        }

        return $item;
    }

    public function insertOne(array $item): array
    {
        if (
            isset($this[static::OPTION__REWRITE]) 
            && ($this[static::OPTION__REWRITE] == static::REWRITE_OFF) 
            && isset($item[IHaveId::FIELD__ID])
        ) {
            return $item;
        }

        $item[IHaveId::FIELD__ID] = Uuid::uuid6()->toString();

        return $item;
    }

    public function insertMany(array $item): array
    {
        return $this->insertOne($item);
    }
}
