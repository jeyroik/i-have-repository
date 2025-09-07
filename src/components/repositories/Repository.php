<?php
namespace jeyroik\components\repositories;

use jeyroik\components\attributes\THasNowDate;
use jeyroik\interfaces\repositories\IRepository;

abstract class Repository implements IRepository
{
    use THasNowDate;

    protected string $dsn = '';

    protected string $dbName = '';
    protected string $tableName = '';
    protected string $itemClass = '';

    protected string $dsnPrefix = '_';
    protected string $dsnSuffix = '';

    protected array $plugins = [];
    protected bool $arePluginsInitialized = false;

    public function __construct(string $dbName, string $tableName, string $itemClass)
    {
        $this->setDbName($dbName)->setTableName($tableName)->setItemClass($itemClass);
        $this->init();
    }

    abstract protected function init(): void;

    protected function setTableName(string $tableName): static
    {
        $this->tableName = $tableName;

        return $this;
    }

    protected function getTableName(): string
    {
        return $this->tableName;
    }

    protected function setDbName(string $dbName): static
    {
        $this->dbName = $dbName;

        return $this;
    }

    protected function getDbName(): string
    {
        return $this->dbName;
    }

    protected function setItemClass(string $itemClass): static
    {
        $this->itemClass = $itemClass;

        return $this;
    }

    protected function getItemClass(): string
    {
        return $this->itemClass;
    }

    protected function applyPlugins(string $method, array $item): array
    {
        if (!$this->arePluginsInitialized) {
            $this->initPlugins();
        }

        foreach ($this->plugins as $plugin) {
            $item = method_exists($plugin, $method) ? $plugin->$method($item) : $plugin($method, $item);
        }

        return $item;
    }

    protected function initPlugins(): void
    {
        $this->arePluginsInitialized = true;
        $pluginsPath = $this->getPluginsPath();
        if (empty($pluginsPath)) {
            return;
        } elseif (is_file($pluginsPath)) {
            $pluginsConfigs = include $pluginsPath;
            foreach ($pluginsConfigs as $class => $options) {
                $this->plugins[] = new $class($options);
            }
        } else {
            throw new \Exception('Missed repository plugins file! Please, set REPOSITORY__PLUGINS_FILE env or const');
        }
    }

    protected function getPluginsPath(): string
    {
        return defined('REPOSITORY__PLUGINS_FILE') 
                ? REPOSITORY__PLUGINS_FILE 
                : (getenv('REPOSITORY__PLUGINS_FILE') ?: '');
    }
}
