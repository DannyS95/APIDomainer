<?php

declare(strict_types=1);

namespace Tests\Stub;

final class FakeObjectRepository
{
    /**
     * @param array<int, array<string, mixed>> $rows
     */
    public function __construct(private array $rows)
    {
    }

    public function find(int $id): ?object
    {
        foreach ($this->rows as $row) {
            if (($row['id'] ?? null) === $id) {
                return (object) $row;
            }
        }

        return null;
    }
}
