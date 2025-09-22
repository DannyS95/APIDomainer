<?php

declare(strict_types=1);

namespace Doctrine\Common\Collections;

interface Collection
{
    public function contains(mixed $element): bool;

    public function add(mixed $element): void;

    public function removeElement(mixed $element): void;
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
}
