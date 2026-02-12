<?php

declare(strict_types=1);

require_once __DIR__ . '/../../../TestBootstrap.php';

use App\Application\Query\GetRobotDanceOffTeamsQuery;
use App\Application\Query\Handler\GetRobotDanceOffTeamsQueryHandler;
use App\Domain\ReadModel\RobotBattleViewInterface;
use App\Domain\Repository\RobotBattleViewReadRepositoryInterface;
use App\Domain\ValueObject\FilterCriteria;

function assertTrue(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

$repository = new class() implements RobotBattleViewReadRepositoryInterface {
    public ?FilterCriteria $criteria = null;

    /**
     * @return list<RobotBattleViewInterface>
     */
    public function findByCriteria(FilterCriteria $filterCriteria): array
    {
        $this->criteria = $filterCriteria;

        return [];
    }
};

$handler = new GetRobotDanceOffTeamsQueryHandler($repository);
$result = $handler(new GetRobotDanceOffTeamsQuery(42));

assertTrue($repository->criteria instanceof FilterCriteria, 'Handler should send criteria to repository.');
assertTrue($repository->criteria->getFilters() === ['battleId' => 42], 'Handler should filter by battle id.');
assertTrue($repository->criteria->getOperations() === ['battleId' => 'eq'], 'Handler should use eq operation.');
assertTrue($repository->criteria->getSorts() === ['createdAt' => 'DESC'], 'Handler should use the newest-first sort.');
assertTrue($repository->criteria->getPage() === 1, 'Handler should use first page.');
assertTrue($repository->criteria->getItemsPerPage() === 50, 'Handler should cap list size to 50.');
assertTrue($result === [], 'Handler should return repository results.');

echo "GetRobotDanceOffTeamsQueryHandler tests completed successfully.\n";
