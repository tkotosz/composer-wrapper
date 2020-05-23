<?php

namespace Tkotosz\ComposerWrapper\Composer;

class Package
{
    /** @var string */
    private $name;

    /** @var string */
    private $version;

    /** @var string */
    private $type;

    public static function fromValues(string $name, string $version, string $type): Package
    {
        return new self($name, $version, $type);
    }

    public function name(): string
    {
        return $this->name;
    }

    public function version(): string
    {
        return $this->version;
    }

    public function type(): string
    {
        return $this->type;
    }

    private function __construct(string $name, string $version, string $type)
    {
        $this->name = $name;
        $this->version = $version;
        $this->type = $type;
    }
}