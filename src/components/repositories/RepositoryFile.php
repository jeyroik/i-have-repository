<?php
namespace jeyroik\components\repositories;

use jeyroik\components\THasAttributes;
use jeyroik\interfaces\IHaveAttributes;
use jeyroik\interfaces\attributes\IHaveId;

class RepositoryFile extends Repository
{
    protected string $dsn = '';
    protected string $itemClass = '';

    protected string $dsnPrefix = '/tmp/db.';
    protected string $dsnSuffix = '.json';

    public function init(): void
    {
        if (!file_exists($this->getDsn())) {
            $this->saveTable();
        }
    }

    public function findOne(array $where = [], array $orderBy = []): ?IHaveAttributes
    {
        $found = $this->filterByWhere($this->getTable(), $where);
     
        if (empty($found)) {
            return null;
        }

        if (!empty($orderBy)) {
            $found = $this->orderBy($found, $orderBy);
        }

        $itemClass = $this->itemClass;
        return new $itemClass(
            $this->applyPlugins(
                'findOne',
                array_shift($found)
            )
        );
    }

    public function findAll(array $where = [], array $orderBy = [], int $offset = 0, int $limit = 0): array
    {
        $found = $this->filterByWhere($this->getTable(), $where);

        if (!empty($orderBy)) {
            $found = $this->orderBy($found, $orderBy);
        }

        return array_map(function($item) {
            $itemClass = $this->itemClass;
            return new $itemClass($this->applyPlugins('findAll', $item));
        }, $found);
    }

    public function insertOne(array $data): ?IHaveAttributes
    {
        $data = $this->applyPlugins('insertOne', $data);
        $table = $this->getTable();
        $table[] = $data;
        $this->saveTable($table);

        $itemClass = $this->itemClass;

        return new $itemClass($data);
    }

    public function insertMany(array $items): array
    {
        $result = [];
        
        foreach ($items as $item) {
            $result[] = $this->insertOne($item);
        }

        return $result;
    }

    public function updateOne(IHaveId $item): void
    {
        $table = $this->getTable();
        $byId = array_column($table, null, IHaveId::FIELD__ID);

        $byId[$item[IHaveId::FIELD__ID]] = $this->applyPlugins('update', $item->__toArray());

        $table = array_values($byId);

        $this->saveTable($table);
    }

    public function updateMany(array $where, array $values): void
    {
        $found = $this->filterByWhere($this->getTable(), $where);

        foreach ($found as $i => $item) {
            foreach ($values as $key => $value) {
                if (isset($item[$key])) {
                    $found[$i][$key] = $value;
                }
            }
        }

        foreach ($found as $item) {
            $this->updateOne(new class ($item) implements IHaveId {
                use THasAttributes;
            });
        }
    }

    public function deleteOne(IHaveId $item): void
    {
        $item = $this->applyPlugins('delete', $item->__toArray());
        $table = $this->getTable();
        $byId = array_column($table, null, 'id');

        unset($byId[$item[IHaveId::FIELD__ID]]);

        $table = array_values($byId);

        $this->saveTable($table);
    }

    public function deleteMany(array $where): void
    {
        $found = $this->filterByWhere($this->getTable(), $where);

        foreach ($found as $item) {
            $this->deleteOne(new class ($item) implements IHaveId {
                use THasAttributes;
            });
        }
    }

    public function truncate(): void
    {
        $this->saveTable([]);
    }

    protected function orderBy(array $found, array $orderBy): array
    {
        $orderedBy = array_column($found, null, array_shift($orderBy));
        $direction = array_shift($orderBy);
        $direction == static::ORDER__ASC ? ksort($orderedBy) : krsort($orderedBy);
        
        return $orderedBy;
    }

    protected function filterByWhere(array $found, array $where): array
    {
        foreach($where as $field => $value) {
            $found = array_filter($found, function($item) use ($field, $value) {
                return $item[$field] == $value;
            });
        }

        return $found;
    }

    protected function getTable(): array
    {
        return json_decode(file_get_contents($this->getDsn()), true);
    }

    protected function saveTable(array $table = []): void
    {
        file_put_contents($this->getDsn(), json_encode($table));
    }

    protected function getDsn(): string
    {
        if (!$this->dsn) {
            $this->dsn = $this->getDbName();
        }

        return $this->dsnPrefix . $this->dsn . $this->dsnSuffix;
    }
}
