<?php

use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Filesystem\Filesystem;
use Tkotosz\ComposerWrapper\Composer;

require 'vendor/autoload.php';

$fileSystem = new Filesystem();
$input = new ArgvInput();
$output = new BufferedOutput();

$composer = new Composer($fileSystem, $input, $output, 'test.json');

echo PHP_EOL;

$result = $composer->init('vendor-test');

if ($result === 0) {
    echo "Init Success" . PHP_EOL;
} else {
    echo "Init Failed" . PHP_EOL;
}

echo PHP_EOL;

$result = $composer->installPackage('php', '>=7.2');

if ($result === 0) {
    echo "php require Success" . PHP_EOL;
} else {
    echo "php require Failed" . PHP_EOL;
}

echo PHP_EOL;

$result = $composer->installPackage('symfony/console');

if ($result === 0) {
    echo "symfony/console install Success" . PHP_EOL;
} else {
    echo "symfony/console install Failed" . PHP_EOL;
}

echo PHP_EOL;

$result = $composer->installPackage('symfony/filesystem');

if ($result === 0) {
    echo "symfony/filesystem install Success" . PHP_EOL;
} else {
    echo "symfony/filesystem install Failed" . PHP_EOL;
}

echo PHP_EOL;

$result = $composer->findInstalledPackages();

echo "Installed Packages:" . PHP_EOL;
foreach ($result as $package) {
    echo "- " . $package->getName() . PHP_EOL;
}

echo PHP_EOL;

$result = $composer->removePackage('symfony/console');

if ($result === 0) {
    echo "symfony/console remove Success" . PHP_EOL;
} else {
    echo "symfony/console remove Failed" . PHP_EOL;
}

echo PHP_EOL;

$result = $composer->findInstalledPackages();

echo "Installed Packages:" . PHP_EOL;
foreach ($result as $package) {
    echo "- " . $package->getName() . PHP_EOL;
}

echo PHP_EOL;

$result = $composer->findInstalledPackages()->filterByType('library');

echo "Installed Library Packages:" . PHP_EOL;
foreach ($result as $package) {
    echo "- " . $package->getName() . PHP_EOL;
}

echo PHP_EOL;

$result = $composer->findPackagesByType('magento2-module');

echo "Magento 2 Packages:" . PHP_EOL;
foreach ($result as $package) {
    echo "- " . $package->getName() . PHP_EOL;
}

echo PHP_EOL;

$result = $composer->changeComposerConfig(
    $composer->getComposerConfig()->addRepository('foo', 'vcs', 'https://github.com/tkotosz/fooapp.git')
);

if ($result === 0) {
    echo "Custom composer repository (foo) add success" . PHP_EOL;
    echo file_get_contents('test.json') . PHP_EOL;
} else {
    echo "Custom composer repository (foo) add failed" . PHP_EOL;
}

echo PHP_EOL;

$result = $composer->changeComposerConfig(
    $composer->getComposerConfig()->removeRepository('foo')
);

if ($result === 0) {
    echo "Custom composer repository (foo) remove success" . PHP_EOL;
    echo file_get_contents('test.json') . PHP_EOL;
} else {
    echo "Custom composer repository (foo) remove failed" . PHP_EOL;
}

echo PHP_EOL;
