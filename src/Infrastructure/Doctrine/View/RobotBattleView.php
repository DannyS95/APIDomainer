<?php

namespace App\Infrastructure\Doctrine\View;

use App\Domain\ReadModel\RobotBattleViewInterface;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(readOnly: true)]
#[ORM\Table(name: 'robot_battle_view')]
final class RobotBattleView implements RobotBattleViewInterface
{
    #[ORM\Id]
    #[ORM\Column(name: 'battle_id', type: 'integer')]
    private int $id;

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(name: 'team_one_id', type: 'integer')]
    private int $teamOneId;

    #[ORM\Column(name: 'team_one_name', type: 'string', length: 100)]
    private string $teamOneName;

    /**
     * @var list<array<string, mixed>> Plain robot payload for team one pulled from the view JSON column
     */
    #[ORM\Column(name: 'team_one_robots', type: 'json', nullable: false)]
    private array $teamOneRobots = [];

    #[ORM\Column(name: 'team_two_id', type: 'integer')]
    private int $teamTwoId;

    #[ORM\Column(name: 'team_two_name', type: 'string', length: 100)]
    private string $teamTwoName;

    /**
     * @var list<array<string, mixed>> Plain robot payload for team two pulled from the view JSON column
     */
    #[ORM\Column(name: 'team_two_robots', type: 'json', nullable: false)]
    private array $teamTwoRobots = [];

    #[ORM\Column(name: 'winning_team_id', type: 'integer', nullable: true)]
    private ?int $winningTeamId = null;

    #[ORM\Column(name: 'winning_team_name', type: 'string', length: 100, nullable: true)]
    private ?string $winningTeamName = null;

    public function __construct()
    {
    }

    public static function fromData(
        int $id,
        DateTimeImmutable $createdAt,
        int $teamOneId,
        string $teamOneName,
        array $teamOneRobots,
        int $teamTwoId,
        string $teamTwoName,
        array $teamTwoRobots,
        ?int $winningTeamId,
        ?string $winningTeamName
    ): self {
        $instance = new self();
        $instance->id = $id;
        $instance->createdAt = $createdAt;
        $instance->teamOneId = $teamOneId;
        $instance->teamOneName = $teamOneName;
        $instance->teamOneRobots = $teamOneRobots;
        $instance->teamTwoId = $teamTwoId;
        $instance->teamTwoName = $teamTwoName;
        $instance->teamTwoRobots = $teamTwoRobots;
        $instance->winningTeamId = $winningTeamId;
        $instance->winningTeamName = $winningTeamName;

        return $instance;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getTeamOne(): array
    {
        return $this->buildTeamData($this->teamOneId, $this->teamOneName, $this->teamOneRobots);
    }

    public function getTeamTwo(): array
    {
        return $this->buildTeamData($this->teamTwoId, $this->teamTwoName, $this->teamTwoRobots);
    }

    public function getWinningTeam(): ?array
    {
        if ($this->winningTeamId === null) {
            return null;
        }

        if ($this->winningTeamId === $this->teamOneId) {
            return $this->getTeamOne();
        }

        if ($this->winningTeamId === $this->teamTwoId) {
            return $this->getTeamTwo();
        }

        return $this->buildTeamData($this->winningTeamId, $this->winningTeamName ?? 'Unknown Team', []);
    }

    /**
     * @param list<array<string, mixed>>|null $robots
     */
    private function buildTeamData(int $id, string $name, ?array $robots): array
    {
        return [
            'id' => $id,
            'name' => $name,
            'robots' => $robots ?? [],
        ];
    }
}
