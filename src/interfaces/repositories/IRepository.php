<?php
namespace jeyroik\interfaces\repositories;

use jeyroik\interfaces\IHaveAttributes;
use jeyroik\interfaces\attributes\IHaveId;

interface IRepository
{
    public const ORDER__ASC = 'asc';
    public const ORDER__DESC = 'desc';

    public const NOT_EQUAL = '!=';
    public const GREATER = '>';
    public const GREATER_OR_EQUAL = '>=';
    public const LOWER = '<';
    public const LOWER_OR_EQUAL = '<=';
    public const IN = 'in';
    public const NOT_IN = 'nin';

    public const ALL_OPERATIONS = [
        self::NOT_EQUAL,
        self::GREATER,
        self::GREATER_OR_EQUAL,
        self::LOWER,
        self::LOWER_OR_EQUAL,
        self::IN,
        self::NOT_IN
    ];

    public function findOne(array $where = [], array $orderBy = []): ?IHaveAttributes;

    public function findAll(array $where = [], array $orderBy = [], int $offset = 0, int $limit = 0): array;

    public function insertOne(array $data): ?IHaveAttributes;
    public function insertMany(array $items): array;

    public function updateOne(IHaveId $item): void;
    public function updateMany(array $where, array $values): void;

    public function deleteOne(IHaveId $item): void;
    public function deleteMany(array $where): void;

    public function truncate(): void;

    
}
