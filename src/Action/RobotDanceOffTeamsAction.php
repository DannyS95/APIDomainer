<?php

namespace App\Action;

use App\Domain\ReadModel\RobotBattleViewInterface;
use App\Domain\Repository\RobotDanceOffRepositoryInterface;
use App\Domain\ValueObject\FilterCriteria;
use App\Infrastructure\Response\RobotDanceOffTeamsResponse;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
final class RobotDanceOffTeamsAction
{
    public function __construct(private RobotDanceOffRepositoryInterface $robotDanceOffRepository)
    {
    }

    /**
     * @return array<int, RobotDanceOffTeamsResponse>
     */
    public function __invoke(Request $request): array
    {
        $battleId = (int) $request->attributes->get('battleId', 0);

        if ($battleId <= 0) {
            throw new BadRequestHttpException('Battle ID must be a positive integer.');
        }

        $filterCriteria = new FilterCriteria(
            ['battleId' => $battleId],
            ['battleId' => 'eq'],
            ['createdAt' => 'DESC'],
            1,
            50
        );

        $danceOffs = $this->robotDanceOffRepository->findAll($filterCriteria);

        return array_map(
            static fn (RobotBattleViewInterface $danceOff): RobotDanceOffTeamsResponse => new RobotDanceOffTeamsResponse(
                $danceOff->getBattleId(),
                $danceOff->getId(),
                $danceOff->getTeamOneRobotIds(),
                $danceOff->getTeamTwoRobotIds()
            ),
            $danceOffs
        );
    }
}
