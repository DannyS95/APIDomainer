<?php

declare(strict_types=1);

require_once __DIR__ . '/../TestBootstrap.php';

use App\Application\DTO\ApiFiltersDTO;
use App\Domain\Entity\Robot;
use App\Domain\Entity\RobotDanceOff;
use App\Infrastructure\Repository\RobotDanceOffQueryBuilder;
use App\Infrastructure\Repository\RobotDanceOffRepository;
use App\Infrastructure\Repository\RobotQueryBuilder;
use App\Infrastructure\Repository\RobotRepository;
use Tests\Stub\FakeDoctrineRepository;
use Tests\Stub\FakeEntityManager;

function assertTrue(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

$datasets = [
    Robot::class => [
        ['id' => 1, 'name' => 'Alpha', 'experience' => 10, 'outOfOrder' => false],
        ['id' => 2, 'name' => 'Beta', 'experience' => 4, 'outOfOrder' => true],
        ['id' => 3, 'name' => 'Gamma', 'experience' => 7, 'outOfOrder' => false],
    ],
    RobotDanceOff::class => [
        ['id' => 1, 'title' => 'Qualifier', 'winningTeam' => 'Team One'],
        ['id' => 2, 'title' => 'Final', 'winningTeam' => 'Team Two'],
    ],
];

$entityManager = new FakeEntityManager($datasets);

$robotRepository = new RobotRepository(
    new RobotQueryBuilder($entityManager),
    new FakeDoctrineRepository()
);

$firstRobots = $robotRepository->findAll(new ApiFiltersDTO(
    ['outOfOrder' => false],
    [],
    [],
    1,
    10
));
assertTrue(count($firstRobots) === 2, 'First robot query should return two robots.');

$secondRobots = $robotRepository->findAll(new ApiFiltersDTO(
    ['outOfOrder' => true],
    [],
    [],
    1,
    10
));
assertTrue(count($secondRobots) === 1, 'Second robot query should return one robot.');
assertTrue($secondRobots[0]['name'] === 'Beta', 'Filtered robot should be Beta.');

$likeFilteredRobots = $robotRepository->findAll(new ApiFiltersDTO(
    ['name' => 'amm'],
    ['name' => 'lk'],
    [],
    1,
    10
));
assertTrue(count($likeFilteredRobots) === 1 && $likeFilteredRobots[0]['name'] === 'Gamma', 'LIKE operation should match Gamma robot.');

$experiencedRobots = $robotRepository->findAll(new ApiFiltersDTO(
    ['experience' => 5],
    ['experience' => 'gt'],
    [],
    1,
    10
));
assertTrue(count($experiencedRobots) === 2, 'Greater-than filter should return two robots.');

$builder = (new RobotQueryBuilder($entityManager))->create();
$combinedFilters = $builder
    ->whereClauses(['outOfOrder' => false], [])
    ->whereClauses(['experience' => 7], [])
    ->fetchArray();
assertTrue(count($combinedFilters) === 1 && $combinedFilters[0]['name'] === 'Gamma', 'Chained whereClauses should combine filters.');

$sharedBuilder = new RobotQueryBuilder($entityManager);
$sharedBuilder->whereClauses(['outOfOrder' => false], [])->fetchArray();
$resetResult = $sharedBuilder->whereClauses(['outOfOrder' => true], [])->fetchArray();
assertTrue(count($resetResult) === 1 && $resetResult[0]['name'] === 'Beta', 'Builder should reset state between invocations.');

$robot = (new RobotQueryBuilder($entityManager))
    ->create()
    ->whereClauses(['outOfOrder' => false], [])
    ->whereId(3)
    ->fetchOne();
assertTrue($robot !== null && $robot->getName() === 'Gamma', 'whereId should work alongside existing filters.');
assertTrue($robot->getExperience() === 7, 'findOneBy should hydrate experience property.');

$robotDanceOffRepository = new RobotDanceOffRepository(
    $entityManager,
    new RobotDanceOffQueryBuilder($entityManager)
);

$firstDance = $robotDanceOffRepository->findAll(new ApiFiltersDTO(
    ['id' => 1],
    [],
    [],
    1,
    10
));
assertTrue(count($firstDance) === 1 && $firstDance[0]['id'] === 1, 'First dance-off query should return ID 1.');

$secondDance = $robotDanceOffRepository->findAll(new ApiFiltersDTO(
    ['id' => 2],
    [],
    [],
    1,
    10
));
assertTrue(count($secondDance) === 1 && $secondDance[0]['id'] === 2, 'Second dance-off query should return ID 2.');

echo "Repository tests completed successfully.\n";
