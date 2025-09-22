<?php

declare(strict_types=1);

require_once __DIR__ . '/../TestBootstrap.php';

use App\Domain\Entity\Robot;
use App\Domain\Entity\RobotDanceOff;
use App\Domain\Entity\Team;
use App\Infrastructure\Response\RobotDanceOffResponse;
use App\Responder\RobotDanceOffResponder;
use Doctrine\Common\Collections\Collection;

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

function createRobot(int $id, string $name, string $powermove, int $experience, bool $outOfOrder, ?string $avatar): Robot
{
    $robot = new Robot();
    setProperty($robot, 'id', $id);
    $robot
        ->setName($name)
        ->setPowermove($powermove)
        ->setExperience($experience)
        ->setOutOfOrder($outOfOrder)
        ->setAvatar($avatar);

    return $robot;
}

function createTeam(int $id, string $name, Robot ...$robots): Team
{
    $team = new Team($name);
    setProperty($team, 'id', $id);

    foreach ($robots as $robot) {
        $team->addRobot($robot);
    }

    return $team;
}

$robotOne = createRobot(1, 'Atlas', 'Flare', 12, false, 'atlas.png');
$robotTwo = createRobot(2, 'Bolt', 'Slide', 8, false, 'bolt.png');
$robotThree = createRobot(3, 'Circuit', 'Spin', 6, true, 'circuit.png');

$teamAlpha = createTeam(10, 'Alpha Team', $robotOne, $robotTwo);
$teamBeta = createTeam(20, 'Beta Squad', $robotThree);

$danceOff = new RobotDanceOff();
setProperty($danceOff, 'id', 42);
setProperty($danceOff, 'createdAt', new \DateTime('2024-03-01 09:30:00'));
$danceOff->setTeamOne($teamAlpha);
$danceOff->setTeamTwo($teamBeta);
$danceOff->setWinningTeam($teamAlpha);

$responder = new RobotDanceOffResponder();
$responses = $responder->respond([$danceOff]);

assertTrue($responses instanceof Collection, 'Responder should return a Doctrine Collection.');
assertTrue($responses->count() === 1, 'Responder should produce one response item.');

$firstResponse = $responses->first();
assertTrue($firstResponse instanceof RobotDanceOffResponse, 'Responder should map entities to RobotDanceOffResponse objects.');
assertTrue($firstResponse->getId() === 42, 'Response should expose the dance-off ID.');

$teamOneDetails = $firstResponse->getTeamOne();
assertTrue($teamOneDetails['id'] === 10, 'Team One ID should match the entity.');
assertTrue($teamOneDetails['name'] === 'Alpha Team', 'Team One name should match the entity.');
assertTrue(count($teamOneDetails['robots']) === 2, 'Team One should list both robots.');
$firstRobot = $teamOneDetails['robots'][0];
assertTrue($firstRobot['id'] === 1, 'Mapped robot should include its ID.');
assertTrue($firstRobot['powermove'] === 'Flare', 'Mapped robot should include powermove details.');

$winningTeamDetails = $firstResponse->getWinningTeam();
assertTrue($winningTeamDetails !== null, 'Winning team should be present.');
assertTrue($winningTeamDetails['name'] === 'Alpha Team', 'Winning team name should match the entity.');
assertTrue(count($winningTeamDetails['robots']) === 2, 'Winning team robots should be mapped.');

$teamTwoDetails = $firstResponse->getTeamTwo();
assertTrue($teamTwoDetails['id'] === 20, 'Team Two ID should match the entity.');
assertTrue(count($teamTwoDetails['robots']) === 1, 'Team Two should list its single robot.');
assertTrue($teamTwoDetails['robots'][0]['outOfOrder'] === true, 'Robot attributes should include the outOfOrder flag.');

echo "RobotDanceOffResponder tests completed successfully.\n";
