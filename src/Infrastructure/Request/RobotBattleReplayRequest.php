<?php

namespace App\Infrastructure\Request;

use Symfony\Component\Validator\Constraints as Assert;

final class RobotBattleReplayRequest
{
    #[Assert\NotNull]
    #[Assert\Type(type: 'integer')]
    #[Assert\Positive]
    public int $battleId;

    /**
     * @var array<int, array{out: int, in: int}>
     */
    #[Assert\Count(max: 2)]
    #[Assert\All(constraints: [
        new Assert\Collection(fields: [
            'out' => new Assert\Required([
                new Assert\Type(type: 'integer'),
                new Assert\Positive(),
            ]),
            'in' => new Assert\Required([
                new Assert\Type(type: 'integer'),
                new Assert\Positive(),
            ]),
        ], allowExtraFields: false),
    ])]
    public array $teamAReplacements = [];

    /**
     * @var array<int, array{out: int, in: int}>
     */
    #[Assert\Count(max: 2)]
    #[Assert\All(constraints: [
        new Assert\Collection(fields: [
            'out' => new Assert\Required([
                new Assert\Type(type: 'integer'),
                new Assert\Positive(),
            ]),
            'in' => new Assert\Required([
                new Assert\Type(type: 'integer'),
                new Assert\Positive(),
            ]),
        ], allowExtraFields: false),
    ])]
    public array $teamBReplacements = [];
}
