<?php

namespace Tkotosz\ComposerWrapper\Composer;

use ArrayIterator;
use Composer\Package\PackageInterface;
use IteratorAggregate;
use Traversable;

final class Packages implements IteratorAggregate
{
    /** @var PackageInterface[] */
    private $items;

    /**
     * @param PackageInterface[] $items
     *
     * @return self
     */
    public static function fromItems(array $items): self
    {
        return new self($items);
    }

    /**
     * @return PackageInterface[]
     */
    public function items(): array
    {
        return $this->items;
    }

    /**
     * @return Traversable|PackageInterface[]
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->items);
    }

    public function filterByType(string $type): Packages
    {
        $items = array_filter($this->items, function (PackageInterface $package) use ($type) {
            return $package->getType() === $type;
        });

        return self::fromItems($items);
    }

    public function toNameVersionParis(): array
    {
        $pairs = [];

        foreach ($this->items as $package) {
            $pairs[$package->getName()] = $package->getVersion();
        }

        return $pairs;
    }

    private function __construct(array $items)
    {
        $this->items = $items;
    }
}
