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
            if (is_array($row) && ($row['id'] ?? null) === $id) {
                return (object) $row;
            }

            if (is_object($row) && $this->extractId($row) === $id) {
                return $row;
            }
        }

        return null;
    }

    private function extractId(object $entity): ?int
    {
        if (method_exists($entity, 'getId')) {
            $id = $entity->getId();

            return $id !== null ? (int) $id : null;
        }

        $reflection = new \ReflectionObject($entity);
        if ($reflection->hasProperty('id')) {
            $property = $reflection->getProperty('id');
            $property->setAccessible(true);

            $value = $property->getValue($entity);

            return $value !== null ? (int) $value : null;
        }

        return null;
    }
}
