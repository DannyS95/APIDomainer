<?php

declare(strict_types=1);

namespace Doctrine\ORM\Mapping;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
final class Entity
{
    public function __construct(public ?string $repositoryClass = null)
    {
    }
}

#[Attribute(Attribute::TARGET_CLASS)]
final class Table
{
    public function __construct(public ?string $name = null)
    {
    }
}

#[Attribute(Attribute::TARGET_PROPERTY)]
final class Id
{
    public function __construct()
    {
    }
}

#[Attribute(Attribute::TARGET_PROPERTY)]
final class GeneratedValue
{
    public function __construct()
    {
    }
}

#[Attribute(Attribute::TARGET_PROPERTY)]
final class Column
{
    public function __construct(
        public ?string $type = null,
        public ?int $length = null,
        public bool $nullable = false
    ) {
    }
}

#[Attribute(Attribute::TARGET_PROPERTY)]
final class ManyToMany
{
    public function __construct(
        public ?string $mappedBy = null,
        public ?string $targetEntity = null
    ) {
    }
}

#[Attribute(Attribute::TARGET_PROPERTY)]
final class JoinTable
{
    public function __construct(public ?string $name = null)
    {
    }
}

#[Attribute(Attribute::TARGET_PROPERTY)]
final class ManyToOne
{
    public function __construct(
        public ?string $targetEntity = null,
        public array $cascade = []
    ) {
    }
}

#[Attribute(Attribute::TARGET_PROPERTY)]
final class JoinColumn
{
    public function __construct(
        public ?string $name = null,
        public ?string $referencedColumnName = null,
        public bool $nullable = true
    ) {
    }
}
