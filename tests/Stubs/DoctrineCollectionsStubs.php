<?php

declare(strict_types=1);

namespace Doctrine\Common\Collections;

interface Collection
{
    public function contains(mixed $element): bool;

    public function add(mixed $element): void;

    public function removeElement(mixed $element): void;

    public function map(callable $callback): self;

    public function toArray(): array;

    public function count(): int;

    public function first(): mixed;
}

final class ArrayCollection implements Collection
{
    /** @var array<int, mixed> */
    private array $elements;

    public function __construct(array $elements = [])
    {
        $this->elements = array_values($elements);
    }

    public function contains(mixed $element): bool
    {
        return in_array($element, $this->elements, true);
    }

    public function add(mixed $element): void
    {
        if (!$this->contains($element)) {
            $this->elements[] = $element;
        }
    }

    public function removeElement(mixed $element): void
    {
        $this->elements = array_values(
            array_filter(
                $this->elements,
                static fn (mixed $existing): bool => $existing !== $element
            )
        );
    }

    public function map(callable $callback): Collection
    {
        return new self(array_map($callback, $this->elements));
    }

    public function toArray(): array
    {
        return $this->elements;
    }

    public function count(): int
    {
        return count($this->elements);
    }

    public function first(): mixed
    {
        return $this->elements[0] ?? false;
    }
}
