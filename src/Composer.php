<?php

namespace Tkotosz\ComposerWrapper;

use Composer\Factory;
use Composer\Installer;
use Composer\IO\ConsoleIO;
use Composer\IO\NullIO;
use Composer\Package\PackageInterface;
use Composer\Repository\CompositeRepository;
use Composer\Repository\PlatformRepository;
use Composer\Repository\RepositoryInterface;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Tkotosz\ComposerWrapper\Composer\FileContent;
use Tkotosz\ComposerWrapper\Composer\Package;
use Tkotosz\ComposerWrapper\Composer\Packages;

class Composer
{
    /** @var Filesystem */
    private $filesystem;

    /** @var InputInterface */
    private $input;

    /** @var OutputInterface */
    private $output;

    /** @var string */
    private $filePath;

    public function __construct(
        Filesystem $filesystem,
        InputInterface $input,
        OutputInterface $output,
        string $filePath
    ) {
        $this->filesystem = $filesystem;
        $this->input = $input;
        $this->output = $output;
        $this->filePath = $filePath;
    }

    public function init(string $vendorDir): int
    {
        $this->filesystem->mkdir($vendorDir);

        return $this->changeComposerConfig(
            FileContent::empty()->withVendorDir($vendorDir)
        );
    }

    public function getComposerConfig(): FileContent
    {
        return FileContent::fromString(file_get_contents($this->filePath));
    }

    public function changeComposerConfig(FileContent $fileContent): int
    {
        $originalFileContent = null;
        if ($this->filesystem->exists($this->filePath)) {
            $originalFileContent = FileContent::fromString(file_get_contents($this->filePath));
        }

        $this->filesystem->dumpFile($this->filePath, $fileContent->toString());

        $result = $this->installPackages();

        if ($result !== 0) {
            if ($originalFileContent !== null) {
                $this->filesystem->dumpFile($this->filePath, $fileContent->toString());
            } else {
                $this->filesystem->remove($this->filePath);
            }
        }

        return $result;
    }

    public function installPackage(string $package, string $version = null): int
    {
        // TODO find best version
        $version = $version ?? '*';

        return $this->changeComposerConfig(
            FileContent::fromString(file_get_contents($this->filePath))->addPackage($package, $version)
        );
    }

    public function removePackage(string $package): int
    {
        return $this->changeComposerConfig(
            FileContent::fromString(file_get_contents($this->filePath))->removePackage($package)
        );
    }

    public function installPackages(): int
    {
        return $this->installer()
            ->setUpdate(true)
            ->setWriteLock(false)
            ->setDevMode(false)
            ->setPreferStable(true)
            ->run();
    }

    public function findPackagesByType(string $type): Packages
    {
        $customComposer = Factory::create(new NullIO(), $this->filePath, true);
        $repository = new CompositeRepository($customComposer->getRepositoryManager()->getRepositories());

        // TODO replace this with directly fetching from packagist
        // search uses https://packagist.org/search.json?q=&type=$type
        // but it only returns the first page of results and the search method doesn't support fetching all result
        $packages = [];
        foreach ($repository->search('', RepositoryInterface::SEARCH_FULLTEXT, $type) as $packageInfo) {
            $packages[] = $customComposer->getRepositoryManager()->findPackage($packageInfo['name'], '*');
        }

        return Packages::fromItems($packages);
    }

    public function findInstalledPackages(): Packages
    {
        $customComposer = Factory::create(new NullIO(), $this->filePath, true);

        return Packages::fromItems($customComposer->getRepositoryManager()->getLocalRepository()->getPackages());
    }

    private function installer(): Installer
    {
        $composerIo = new ConsoleIO($this->input, $this->output, new HelperSet([new QuestionHelper()]));
        $customComposer = Factory::create($composerIo, $this->filePath, true);

        return Installer::create($composerIo, $customComposer);
    }
}