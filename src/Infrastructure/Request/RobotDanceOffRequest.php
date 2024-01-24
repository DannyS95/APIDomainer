<?php

namespace App\Infrastructure\Request;

use Symfony\Component\Validator\Constraints as Assert;

final class RobotDanceOffRequest
{
    #[Assert\NotBlank]
    #[Assert\Count(
        min: 5,
        max: 5,
    )]
    #[Assert\All(constraints: [
            new Assert\Type(type: 'integer', message: 'Please make sure the given element is an integer')
        ]
    )]
    #[Assert\All(constraints: [
            new Assert\Positive(),
        ]
    )]
    /**
     * Undocumented variable
     *
     * @var array<int> $teamA
     */
    public array $teamA;
    #[Assert\NotBlank]
    #[Assert\Count(
        min: 5,
        max: 5,
    )]
    #[Assert\All(constraints: [
            new Assert\Type(type: 'integer', message: 'Please make sure the given element is an integer')
        ]
    )]
    #[Assert\All(constraints: [
            new Assert\Positive(),
        ]
    )]
    /**
     * Undocumented variable
     *
     * @var array<int> $teamB
     */
    public array $teamB;
}
