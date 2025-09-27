<?php

declare(strict_types=1);

require_once __DIR__ . '/../TestBootstrap.php';

use App\Application\DTO\ApiFiltersDTO;
use App\Domain\Entity\Robot;
use App\Domain\Entity\RobotDanceOff;
use App\Domain\Entity\Team;
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

function setProperty(object $entity, string $property, mixed $value): void
{
    $reflection = new \ReflectionObject($entity);
    if (!$reflection->hasProperty($property)) {
        return;
    }

    $propertyRef = $reflection->getProperty($property);
    $propertyRef->setAccessible(true);
    $propertyRef->setValue($entity, $value);
}

function createRobotEntity(int $id, string $name, int $experience, bool $outOfOrder, string $powermove): Robot
{
    $robot = new Robot();
    setProperty($robot, 'id', $id);
    $robot
        ->setName($name)
        ->setExperience($experience)
        ->setOutOfOrder($outOfOrder)
        ->setPowermove($powermove)
        ->setAvatar(sprintf('avatar-%d.png', $id));

    return $robot;
}

function createTeamEntity(int $id, string $name, Robot ...$robots): Team
{
    $team = new Team($name);
    setProperty($team, 'id', $id);

    foreach ($robots as $robot) {
        $team->addRobot($robot);
    }

    return $team;
}

function createDanceOffEntity(
    int $id,
    Team $teamOne,
    Team $teamTwo,
    ?Team $winningTeam,
    \DateTime $createdAt
): RobotDanceOff {
    $danceOff = new RobotDanceOff();
    setProperty($danceOff, 'id', $id);
    setProperty($danceOff, 'createdAt', $createdAt);

    $danceOff->setTeamOne($teamOne);
    $danceOff->setTeamTwo($teamTwo);

    if ($winningTeam !== null) {
        $danceOff->setWinningTeam($winningTeam);
    }

    return $danceOff;
}

$alphaRobot = createRobotEntity(1, 'Alpha', 10, false, 'Spin Move');
$betaRobot = createRobotEntity(2, 'Beta', 4, true, 'Power Stomp');
$gammaRobot = createRobotEntity(3, 'Gamma', 7, false, 'Laser Slide');

$teamOne = createTeamEntity(101, 'Team One', $alphaRobot);
$teamTwo = createTeamEntity(102, 'Team Two', $betaRobot);
$teamThree = createTeamEntity(103, 'Team Three', $gammaRobot);

$firstDanceOff = createDanceOffEntity(1, $teamOne, $teamTwo, $teamOne, new \DateTime('2024-01-01 12:00:00'));
$secondDanceOff = createDanceOffEntity(2, $teamTwo, $teamThree, $teamTwo, new \DateTime('2024-01-02 12:00:00'));

$datasets = [
    Robot::class => [
        ['id' => 1, 'name' => 'Alpha', 'experience' => 10, 'outOfOrder' => false],
        ['id' => 2, 'name' => 'Beta', 'experience' => 4, 'outOfOrder' => true],
        ['id' => 3, 'name' => 'Gamma', 'experience' => 7, 'outOfOrder' => false],
    ],
    RobotDanceOff::class => [
        $firstDanceOff,
        $secondDanceOff,
    ],
];

$entityManager = new FakeEntityManager($datasets);

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
    new RobotDanceOffQueryBuilder($entityManager)
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
assertTrue($firstDance[0] instanceof RobotDanceOff, 'Dance-off result should be a RobotDanceOff entity.');
assertTrue($firstDance[0]->getId() === 1, 'First dance-off query should return ID 1.');
assertTrue($firstDance[0]->getWinningTeam()?->getName() === 'Team One', 'Winning team should be Team One.');

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
assertTrue($secondDance[0] instanceof RobotDanceOff, 'Dance-off result should be a RobotDanceOff entity.');
assertTrue($secondDance[0]->getId() === 2, 'Second dance-off query should return ID 2.');
assertTrue($secondDance[0]->getWinningTeam()?->getName() === 'Team Two', 'Winning team should be Team Two.');

echo "Repository tests completed successfully.\n";
