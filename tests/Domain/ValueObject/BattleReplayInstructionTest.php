<?php

declare(strict_types=1);

require_once __DIR__ . '/../../TestBootstrap.php';

use App\Domain\ValueObject\BattleReplayInstruction;
use App\Domain\ValueObject\RobotReplacement;

function assertTrue(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

function assertThrows(callable $callback, string $message): void
{
    try {
        $callback();
    } catch (\InvalidArgumentException) {
        return;
    }

    throw new RuntimeException($message);
}

$instruction = new BattleReplayInstruction(
    5,
    [new RobotReplacement(1, 7), new RobotReplacement(2, 8)],
    [new RobotReplacement(3, 9)]
);

assertTrue($instruction->battleId() === 5, 'Battle id should be preserved.');
assertTrue(count($instruction->teamOneReplacements()) === 2, 'Team one replacements should be returned.');
assertTrue(count($instruction->teamTwoReplacements()) === 1, 'Team two replacements should be returned.');

assertThrows(
    static fn () => new BattleReplayInstruction(
        2,
        [new RobotReplacement(1, 7), new RobotReplacement(2, 8), new RobotReplacement(3, 9)],
        []
    ),
    'Should reject more than two replacements per team.'
);

assertThrows(
    static fn () => new BattleReplayInstruction(
        2,
        [new RobotReplacement(1, 7), new RobotReplacement(1, 8)],
        []
    ),
    'Should reject duplicate outgoing robot identifiers.'
);

assertThrows(
    static fn () => new BattleReplayInstruction(
        2,
        [new RobotReplacement(1, 7), new RobotReplacement(2, 7)],
        []
    ),
    'Should reject duplicate incoming robot identifiers.'
);

assertThrows(
    static fn () => new BattleReplayInstruction(
        2,
        [new RobotReplacement(6, 6)],
        []
    ),
    'Should reject no-op replacements where out equals in.'
);

echo "BattleReplayInstruction tests completed successfully.\n";
