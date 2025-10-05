<?php

declare(strict_types=1);

require_once __DIR__ . '/../TestBootstrap.php';

use App\Infrastructure\Doctrine\View\RobotBattleView;
use App\Infrastructure\Response\RobotDanceOffResponse;
use App\Responder\RobotDanceOffResponder;
use Doctrine\Common\Collections\Collection;

function assertTrue(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

$teamOneRobots = [
    [
        'id' => 1,
        'name' => 'Atlas',
        'powermove' => 'Flare',
        'experience' => 12,
        'outOfOrder' => false,
        'avatar' => 'atlas.png',
    ],
    [
        'id' => 2,
        'name' => 'Bolt',
        'powermove' => 'Slide',
        'experience' => 8,
        'outOfOrder' => false,
        'avatar' => 'bolt.png',
    ],
];

$teamTwoRobots = [
    [
        'id' => 3,
        'name' => 'Circuit',
        'powermove' => 'Spin',
        'experience' => 6,
        'outOfOrder' => true,
        'avatar' => 'circuit.png',
    ],
];

$danceOff = RobotBattleView::fromData(
    id: 42,
    createdAt: new \DateTimeImmutable('2024-03-01 09:30:00'),
    teamOneId: 10,
    teamOneName: 'Alpha Team',
    teamOneRobots: $teamOneRobots,
    teamTwoId: 20,
    teamTwoName: 'Beta Squad',
    teamTwoRobots: $teamTwoRobots,
    winningTeamId: 10,
    winningTeamName: 'Alpha Team'
);

$responder = new RobotDanceOffResponder();
$responses = $responder->respond([$danceOff]);

assertTrue($responses instanceof Collection, 'Responder should return a Doctrine Collection.');
assertTrue($responses->count() === 1, 'Responder should produce one response item.');

$firstResponse = $responses->first();
assertTrue($firstResponse instanceof RobotDanceOffResponse, 'Responder should map entities to RobotDanceOffResponse objects.');
assertTrue($firstResponse->getId() === 42, 'Response should expose the dance-off ID.');

$teamOneDetails = $firstResponse->getTeamOne();
assertTrue($teamOneDetails['id'] === 10, 'Team One ID should match the read model.');
assertTrue($teamOneDetails['name'] === 'Alpha Team', 'Team One name should match the read model.');
assertTrue(count($teamOneDetails['robots']) === 2, 'Team One should list both robots.');
$firstRobot = $teamOneDetails['robots'][0];
assertTrue($firstRobot['id'] === 1, 'Mapped robot should include its ID.');
assertTrue($firstRobot['powermove'] === 'Flare', 'Mapped robot should include powermove details.');

$winningTeamDetails = $firstResponse->getWinningTeam();
assertTrue($winningTeamDetails !== null, 'Winning team should be present.');
assertTrue($winningTeamDetails['name'] === 'Alpha Team', 'Winning team name should match the read model.');
assertTrue(count($winningTeamDetails['robots']) === 2, 'Winning team robots should be mapped.');

$teamTwoDetails = $firstResponse->getTeamTwo();
assertTrue($teamTwoDetails['id'] === 20, 'Team Two ID should match the read model.');
assertTrue(count($teamTwoDetails['robots']) === 1, 'Team Two should list its single robot.');
assertTrue($teamTwoDetails['robots'][0]['outOfOrder'] === true, 'Robot attributes should include the outOfOrder flag.');

echo "RobotDanceOffResponder tests completed successfully.\n";
