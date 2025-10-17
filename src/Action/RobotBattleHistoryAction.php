<?php

namespace App\Action;

use App\Domain\Repository\RobotDanceOffRepositoryInterface;
use App\Domain\ValueObject\FilterCriteria;
use App\Responder\RobotDanceOffResponder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
final class RobotBattleHistoryAction
{
    public function __construct(
        private RobotDanceOffRepositoryInterface $robotDanceOffRepository,
        private RobotDanceOffResponder $robotDanceOffResponder
    ) {
    }

    /**
     * @return array<int, \App\Infrastructure\Response\RobotDanceOffResponse>
     */
    public function __invoke(Request $request): array
    {
        $battleId = (int) $request->attributes->get('battleId', 0);

        $filterCriteria = new FilterCriteria(
            ['battleId' => $battleId],
            ['battleId' => 'eq'],
            ['createdAt' => 'DESC'],
            1,
            50
        );

        $danceOffs = $this->robotDanceOffRepository->findAll($filterCriteria);

        return $this->robotDanceOffResponder->respond($danceOffs)->toArray();
    }
}
