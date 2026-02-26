<?php
declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;
use Rector\Symfony\Set\SymfonySetList;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
        __DIR__ . '/config',
    ]);

    $rectorConfig->skip([
        __DIR__ . '/config/reference.php',
        __DIR__ . '/vendor',
        __DIR__ . '/var',
        __DIR__ . '/public/build',
    ]);

    $rectorConfig->sets([
        LevelSetList::UP_TO_PHP_84,
    ]);

    $rectorConfig->sets([
        SetList::CODE_QUALITY,
        SetList::DEAD_CODE,
        SetList::PRIVATIZATION,
        SetList::NAMING,
        SymfonySetList::SYMFONY_CODE_QUALITY,
    ]);

    $rectorConfig->importNames(false, false);

    $symfonyContainerPhp = __DIR__ . '/var/cache/dev/App_KernelDevDebugContainer.php';
    if (file_exists($symfonyContainerPhp)) {
        $rectorConfig->symfonyContainerPhp($symfonyContainerPhp);
    }
};
