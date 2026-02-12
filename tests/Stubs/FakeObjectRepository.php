<?php

declare(strict_types=1);

namespace Tests\Stub;

final class FakeObjectRepository
{
    /**
     * @param array<int, array<string, mixed>> $rows
     */
    public function __construct(private array $rows, private ?string $entityClass = null)
    {
    }

    public function find(int $id): ?object
    {
        foreach ($this->rows as $row) {
            if (is_array($row) && ($row['id'] ?? null) === $id) {
                return $this->hydrateEntity($row);
            }

            if (is_object($row) && $this->extractId($row) === $id) {
                return $row;
            }
        }

        return null;
    }

    /**
     * @param array<string, mixed> $row
     */
    private function hydrateEntity(array $row): object
    {
        if ($this->entityClass === null || !class_exists($this->entityClass)) {
            return (object) $row;
        }

        $entityClass = $this->entityClass;
        $entity = new $entityClass();

        foreach ($row as $field => $value) {
            $method = 'set' . ucfirst($field);
            if (method_exists($entity, $method)) {
                $entity->$method($value);
                continue;
            }

            $this->setProperty($entity, $field, $value);
        }

        return $entity;
    }

    private function setProperty(object $entity, string $field, mixed $value): void
    {
        $reflection = new \ReflectionObject($entity);
        if (!$reflection->hasProperty($field)) {
            return;
        }

        $property = $reflection->getProperty($field);
        $property->setAccessible(true);
        $property->setValue($entity, $value);
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
