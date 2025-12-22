<?php

declare(strict_types=1);

require_once __DIR__ . '/Stubs/DoctrineMappingStubs.php';
require_once __DIR__ . '/Stubs/DoctrineCollectionsStubs.php';
require_once __DIR__ . '/Stubs/DoctrineComparisonStubs.php';
require_once __DIR__ . '/Stubs/DoctrineOrmStubs.php';
require_once __DIR__ . '/Stubs/DoctrinePersistenceStubs.php';
require_once __DIR__ . '/Stubs/DoctrineBundleStubs.php';

spl_autoload_register(function (string $class): void {
    $prefix = 'App\\';
    $baseDir = __DIR__ . '/../src/';

    if (!str_starts_with($class, $prefix)) {
        return;
    }

    $relativeClass = substr($class, strlen($prefix));
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    if (is_file($file)) {
        require_once $file;
    }
});

// Handle classes that live outside of the PSR-4 directory expectations in this
// lightweight test environment.
require_once __DIR__ . '/../src/Infrastructure/Doctrine/QueryBuilder/DoctrineComparisonEnum.php';

require_once __DIR__ . '/Stubs/FakeEntityManager.php';
require_once __DIR__ . '/Stubs/FakeObjectRepository.php';
require_once __DIR__ . '/Stubs/FakeManagerRegistry.php';
