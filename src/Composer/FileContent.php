<?php

namespace Tkotosz\ComposerWrapper\Composer;

class FileContent
{
    /** @var array */
    private $repositories;

    /** @var string */
    private $minimumStability;

    /** @var bool */
    private $preferStable;

    /** @var array */
    private $config;

    /** @var array */
    private $require;

    /** @var array */
    private $provide;

    public static function fromValues(
        array $repositories,
        string $minimumStability,
        string $preferStable,
        array $config,
        array $require,
        array $provide
    ): FileContent {
        return new self(
            $repositories,
            $minimumStability,
            $preferStable,
            $config,
            $require,
            $provide
        );
    }

    public static function fromArray(array $data): FileContent
    {
        return self::fromValues(
            $data['repositories'] ?? [],
            $data['minimum-stability'] ?? 'dev',
            $data['prefer-stable'] ?? true,
            $data['config'] ?? [],
            $data['require'] ?? [],
            $data['provide'] ?? []
        );
    }

    public static function fromString(string $data): FileContent
    {
        return self::fromArray(json_decode($data, true));
    }

    public static function empty(): FileContent
    {
        return self::fromString('{}');
    }

    public function repositories(): array
    {
        return $this->repositories;
    }

    public function minimumStability(): string
    {
        return $this->minimumStability;
    }

    public function preferStable(): bool
    {
        return $this->preferStable;
    }

    public function config(): array
    {
        return $this->config;
    }

    public function require(): array
    {
        return $this->require;
    }

    public function provide(): array
    {
        return $this->provide;
    }

    public function withVendorDir(string $vendorDir): FileContent
    {
        $newConfig = self::fromArray($this->toArray());

        $newConfig->config['vendor-dir'] = $vendorDir;

        return $newConfig;
    }

    public function addRepository(string $name, string $type, string $url): FileContent
    {
        $newConfig = self::fromArray($this->toArray());

        $newConfig->repositories[$name] = [
            'type' => $type,
            'url' => $url
        ];

        return $newConfig;
    }

    public function addProvide(string $package, string $version): FileContent
    {
        $newConfig = self::fromArray($this->toArray());

        $newConfig->provide[$package] = $version;

        return $newConfig;
    }

    public function addPackage(string $package, string $version): FileContent
    {
        $newConfig = self::fromArray($this->toArray());

        $newConfig->require[$package] = $version;

        return $newConfig;
    }

    public function removePackage(string $package): FileContent
    {
        $newConfig = self::fromArray($this->toArray());

        unset($newConfig->require[$package]);

        return $newConfig;
    }

    private function toArray(): array
    {
        return [
            'repositories' => $this->repositories,
            'minimum-stability' => $this->minimumStability,
            'prefer-stable' => $this->preferStable,
            'config' => $this->config,
            'require' => $this->require,
            'provide' => $this->provide
        ];
    }

    public function toString(): string
    {
        $data = $this->toArray();

        foreach ($data as $key => $value) {
            if ($value === []) {
                unset($data[$key]);
            }
        }

        return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

    private function __construct(
        array $repositories,
        string $minimumStability,
        bool $preferStable,
        array $config,
        array $require,
        array $provide
    ) {

        $this->repositories = $repositories;
        $this->minimumStability = $minimumStability;
        $this->preferStable = $preferStable;
        $this->config = $config;
        $this->require = $require;
        $this->provide = $provide;
    }
}