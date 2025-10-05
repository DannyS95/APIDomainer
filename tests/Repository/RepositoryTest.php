<?php

declare(strict_types=1);

require_once __DIR__ . '/../TestBootstrap.php';

use App\Application\DTO\ApiFiltersDTO;
use App\Domain\Entity\Robot;
use App\Domain\Entity\RobotDanceOff;
use App\Infrastructure\Doctrine\View\RobotBattleView;
use App\Infrastructure\Repository\RobotBattleViewQueryBuilder;
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

$robotDataset = [
    ['id' => 1, 'name' => 'Alpha', 'experience' => 10, 'outOfOrder' => false, 'powermove' => 'Spin Move', 'avatar' => 'avatar-1.png'],
    ['id' => 2, 'name' => 'Beta', 'experience' => 4, 'outOfOrder' => true, 'powermove' => 'Power Stomp', 'avatar' => 'avatar-2.png'],
    ['id' => 3, 'name' => 'Gamma', 'experience' => 7, 'outOfOrder' => false, 'powermove' => 'Laser Slide', 'avatar' => 'avatar-3.png'],
];

$robotBattleDataset = [
    [
        'id' => 1,
        'createdAt' => new \DateTimeImmutable('2024-01-01 12:00:00'),
        'teamOneId' => 101,
        'teamOneName' => 'Team One',
        'teamOneRobots' => [
            ['id' => 1, 'name' => 'Alpha', 'powermove' => 'Spin Move', 'experience' => 10, 'outOfOrder' => false, 'avatar' => 'avatar-1.png'],
        ],
        'teamTwoId' => 102,
        'teamTwoName' => 'Team Two',
        'teamTwoRobots' => [
            ['id' => 2, 'name' => 'Beta', 'powermove' => 'Power Stomp', 'experience' => 4, 'outOfOrder' => true, 'avatar' => 'avatar-2.png'],
        ],
        'winningTeamId' => 101,
        'winningTeamName' => 'Team One',
    ],
    [
        'id' => 2,
        'createdAt' => new \DateTimeImmutable('2024-01-02 12:00:00'),
        'teamOneId' => 102,
        'teamOneName' => 'Team Two',
        'teamOneRobots' => [
            ['id' => 2, 'name' => 'Beta', 'powermove' => 'Power Stomp', 'experience' => 4, 'outOfOrder' => true, 'avatar' => 'avatar-2.png'],
        ],
        'teamTwoId' => 103,
        'teamTwoName' => 'Team Three',
        'teamTwoRobots' => [
            ['id' => 3, 'name' => 'Gamma', 'powermove' => 'Laser Slide', 'experience' => 7, 'outOfOrder' => false, 'avatar' => 'avatar-3.png'],
        ],
        'winningTeamId' => 102,
        'winningTeamName' => 'Team Two',
    ],
];

$danceOffEntities = [
    ['id' => 1],
    ['id' => 2],
];

$entityManager = new FakeEntityManager([
    Robot::class => $robotDataset,
    RobotBattleView::class => $robotBattleDataset,
    RobotDanceOff::class => $danceOffEntities,
]);

$robotRepository = new RobotRepository(
    new RobotQueryBuilder($entityManager),
    new FakeDoctrineRepository()
);

$firstRobots = $robotRepository->findAll(
    (new ApiFiltersDTO(
        ['outOfOrder' => false],
        [],
        [],
        1,
        10
    ))->toFilterCriteria()
);
assertTrue(count($firstRobots) === 2, 'First robot query should return two robots.');

$secondRobots = $robotRepository->findAll(
    (new ApiFiltersDTO(
        ['outOfOrder' => true],
        [],
        [],
        1,
        10
    ))->toFilterCriteria()
);
assertTrue(count($secondRobots) === 1, 'Second robot query should return one robot.');
assertTrue($secondRobots[0]['name'] === 'Beta', 'Filtered robot should be Beta.');

$robot = $robotRepository->findOneBy(3);
assertTrue($robot !== null && $robot->getName() === 'Gamma', 'findOneBy should return Gamma robot.');
assertTrue($robot->getExperience() === 7, 'findOneBy should hydrate experience property.');

$robotDanceOffRepository = new RobotDanceOffRepository(
    $entityManager,
    new RobotBattleViewQueryBuilder($entityManager)
);

$firstDance = $robotDanceOffRepository->findAll(
    (new ApiFiltersDTO(
        ['id' => 1],
        [],
        [],
        1,
        10
    ))->toFilterCriteria()
);
assertTrue(count($firstDance) === 1, 'First dance-off query should return one result.');
assertTrue($firstDance[0] instanceof RobotBattleView, 'Dance-off result should be a RobotBattleView read model.');
assertTrue($firstDance[0]->getId() === 1, 'First dance-off query should return ID 1.');
assertTrue($firstDance[0]->getWinningTeam()['name'] === 'Team One', 'Winning team should be Team One.');

$secondDance = $robotDanceOffRepository->findAll(
    (new ApiFiltersDTO(
        ['id' => 2],
        [],
        [],
        1,
        10
    ))->toFilterCriteria()
);
assertTrue(count($secondDance) === 1, 'Second dance-off query should return one result.');
assertTrue($secondDance[0] instanceof RobotBattleView, 'Dance-off result should be a RobotBattleView read model.');
assertTrue($secondDance[0]->getId() === 2, 'Second dance-off query should return ID 2.');
assertTrue($secondDance[0]->getWinningTeam()['name'] === 'Team Two', 'Winning team should be Team Two.');

echo "Repository tests completed successfully.\n";
