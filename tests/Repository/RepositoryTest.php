<?php

declare(strict_types=1);

require_once __DIR__ . '/../TestBootstrap.php';

use App\Application\DTO\ApiFiltersDTO;
use App\Domain\Entity\Robot;
use App\Domain\Entity\RobotDanceOff;
use App\Domain\ReadModel\RobotBattleView;
use App\Infrastructure\Repository\RobotBattleViewReadRepository;
use App\Infrastructure\Repository\RobotReadRepository;
use App\Infrastructure\Repository\RobotRepository;
use Tests\Stub\FakeEntityManager;
use Tests\Stub\FakeManagerRegistry;

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
        'battleReplayId' => 1,
        'battleId' => 1001,
        'createdAt' => new \DateTimeImmutable('2024-01-01 12:00:00'),
        'teamOneId' => 101,
        'teamOneName' => 'Team One',
        'teamOneCodeName' => 'Team One',
        'teamOneRobots' => [
            ['id' => 1, 'name' => 'Alpha', 'powermove' => 'Spin Move', 'experience' => 10, 'outOfOrder' => false, 'avatar' => 'avatar-1.png'],
        ],
        'teamTwoId' => 102,
        'teamTwoName' => 'Team Two',
        'teamTwoCodeName' => 'Team Two',
        'teamTwoRobots' => [
            ['id' => 2, 'name' => 'Beta', 'powermove' => 'Power Stomp', 'experience' => 4, 'outOfOrder' => true, 'avatar' => 'avatar-2.png'],
        ],
        'winningTeamId' => 101,
        'winningTeamName' => 'Team One',
    ],
    [
        'battleReplayId' => 2,
        'battleId' => 1002,
        'createdAt' => new \DateTimeImmutable('2024-01-02 12:00:00'),
        'teamOneId' => 102,
        'teamOneName' => 'Team Two',
        'teamOneCodeName' => 'Team Two',
        'teamOneRobots' => [
            ['id' => 2, 'name' => 'Beta', 'powermove' => 'Power Stomp', 'experience' => 4, 'outOfOrder' => true, 'avatar' => 'avatar-2.png'],
        ],
        'teamTwoId' => 103,
        'teamTwoName' => 'Team Three',
        'teamTwoCodeName' => 'Team Three',
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
$registry = new FakeManagerRegistry($entityManager);

$robotRepository = new RobotRepository($registry);
$robotReadRepository = new RobotReadRepository($entityManager);

$firstRobots = $robotReadRepository->findByCriteria(
    (new ApiFiltersDTO(
        ['outOfOrder' => false],
        [],
        [],
        1,
        10
    ))->toFilterCriteria()
);
assertTrue(count($firstRobots) === 2, 'First robot query should return two robots.');

$secondRobots = $robotReadRepository->findByCriteria(
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

$robot = $robotRepository->findOneById(3);
assertTrue($robot !== null && $robot->getName() === 'Gamma', 'findOneById should return Gamma robot.');
assertTrue($robot->getExperience() === 7, 'findOneById should hydrate experience property.');

$robotBattleViewReadRepository = new RobotBattleViewReadRepository($entityManager);

$firstDance = $robotBattleViewReadRepository->findByCriteria(
    (new ApiFiltersDTO(
        ['battleReplayId' => 1],
        [],
        [],
        1,
        10
    ))->toFilterCriteria()
);
assertTrue(count($firstDance) === 1, 'First dance-off query should return one result.');
assertTrue($firstDance[0] instanceof RobotBattleView, 'Dance-off result should be a RobotBattleView read model.');
assertTrue($firstDance[0]->getId() === 1, 'First dance-off query should return ID 1.');
assertTrue($firstDance[0]->getBattleId() === 1001, 'First dance-off should reference battle 1001.');
assertTrue($firstDance[0]->getWinningTeam()['name'] === 'Team One', 'Winning team should be Team One.');

$secondDance = $robotBattleViewReadRepository->findByCriteria(
    (new ApiFiltersDTO(
        ['battleReplayId' => 2],
        [],
        [],
        1,
        10
    ))->toFilterCriteria()
);
assertTrue(count($secondDance) === 1, 'Second dance-off query should return one result.');
assertTrue($secondDance[0] instanceof RobotBattleView, 'Dance-off result should be a RobotBattleView read model.');
assertTrue($secondDance[0]->getId() === 2, 'Second dance-off query should return ID 2.');
assertTrue($secondDance[0]->getBattleId() === 1002, 'Second dance-off should reference battle 1002.');
assertTrue($secondDance[0]->getWinningTeam()['name'] === 'Team Two', 'Winning team should be Team Two.');

$battleReplay = $robotBattleViewReadRepository->findByCriteria(
    (new ApiFiltersDTO(
        ['battleId' => 1001],
        [],
        [],
        1,
        10
    ))->toFilterCriteria()
);
assertTrue(count($battleReplay) === 1, 'Filtering by battleId should limit the dataset.');
assertTrue($battleReplay[0]->getBattleId() === 1001, 'Filtered battle should match the requested aggregate.');

$battleReplayByLegacyId = $robotBattleViewReadRepository->findByCriteria(
    (new ApiFiltersDTO(
        ['id' => 2],
        [],
        [],
        1,
        10
    ))->toFilterCriteria()
);
assertTrue(count($battleReplayByLegacyId) === 1, 'Filtering by legacy id alias should return one battle replay.');
assertTrue($battleReplayByLegacyId[0]->getId() === 2, 'Legacy id alias should map to battle replay identifier.');

$battleReplayOrderedByLegacyId = $robotBattleViewReadRepository->findByCriteria(
    (new ApiFiltersDTO(
        [],
        [],
        ['id' => 'DESC'],
        1,
        10
    ))->toFilterCriteria()
);
assertTrue($battleReplayOrderedByLegacyId[0]->getId() === 2, 'Sorting by legacy id alias should map to battle replay identifier.');

echo "Repository tests completed successfully.\n";
