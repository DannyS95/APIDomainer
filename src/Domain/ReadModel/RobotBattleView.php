<?php

namespace App\Domain\ReadModel;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(readOnly: true)]
#[ORM\Table(name: 'robot_battle_view')]
final class RobotBattleView implements RobotBattleViewInterface
{
    #[ORM\Id]
    #[ORM\Column(name: 'battle_replay_id', type: 'integer')]
    private int $battleReplayId;

    #[ORM\Column(name: 'battle_id', type: 'integer')]
    private int $battleId;

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(name: 'team_one_id', type: 'integer')]
    private int $teamOneId;

    #[ORM\Column(name: 'team_one_name', type: 'string', length: 100)]
    private string $teamOneName;

    #[ORM\Column(name: 'team_one_code_name', type: 'string', length: 150)]
    private string $teamOneCodeName;

    #[ORM\Column(name: 'team_one_power', type: 'integer')]
    private int $teamOnePower;

    /**
     * @var list<array<string, mixed>> Plain robot payload for team one pulled from the view JSON column
     */
    #[ORM\Column(name: 'team_one_robots', type: 'json', nullable: false)]
    private array $teamOneRobots = [];

    #[ORM\Column(name: 'team_two_id', type: 'integer')]
    private int $teamTwoId;

    #[ORM\Column(name: 'team_two_name', type: 'string', length: 100)]
    private string $teamTwoName;

    #[ORM\Column(name: 'team_two_code_name', type: 'string', length: 150)]
    private string $teamTwoCodeName;

    #[ORM\Column(name: 'team_two_power', type: 'integer')]
    private int $teamTwoPower;

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

    /**
     * Factory used in tests to hydrate the read model without Doctrine.
     *
     * @param list<array<string, mixed>> $teamOneRobots
     * @param list<array<string, mixed>> $teamTwoRobots
     */
    public static function fromData(
        int $id,
        DateTimeImmutable $createdAt,
        int $teamOneId,
        string $teamOneName,
        string $teamOneCodeName,
        int $teamOnePower,
        array $teamOneRobots,
        int $teamTwoId,
        string $teamTwoName,
        string $teamTwoCodeName,
        int $teamTwoPower,
        array $teamTwoRobots,
        ?int $winningTeamId,
        ?string $winningTeamName,
        int $battleId
    ): self {
        $instance = new self();
        $instance->battleReplayId = $id;
        $instance->battleId = $battleId;
        $instance->createdAt = $createdAt;
        $instance->teamOneId = $teamOneId;
        $instance->teamOneName = $teamOneName;
        $instance->teamOneCodeName = $teamOneCodeName;
        $instance->teamOnePower = $teamOnePower;
        $instance->teamOneRobots = $teamOneRobots;
        $instance->teamTwoId = $teamTwoId;
        $instance->teamTwoName = $teamTwoName;
        $instance->teamTwoCodeName = $teamTwoCodeName;
        $instance->teamTwoPower = $teamTwoPower;
        $instance->teamTwoRobots = $teamTwoRobots;
        $instance->winningTeamId = $winningTeamId;
        $instance->winningTeamName = $winningTeamName;

        return $instance;
    }

    public function getBattleReplayId(): int
    {
        return $this->battleReplayId;
    }

    /**
     * Backwards-compatible alias for the replay identifier.
     */
    public function getId(): int
    {
        return $this->getBattleReplayId();
    }

    public function getBattleId(): int
    {
        return $this->battleId;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getTeamOne(): array
    {
        return $this->buildTeamData($this->teamOneId, $this->teamOneName, $this->teamOneCodeName, $this->teamOneRobots);
    }

    public function getTeamOneCodeName(): string
    {
        return $this->teamOneCodeName;
    }

    public function getTeamOnePower(): int
    {
        return $this->teamOnePower;
    }

    public function getTeamTwo(): array
    {
        return $this->buildTeamData($this->teamTwoId, $this->teamTwoName, $this->teamTwoCodeName, $this->teamTwoRobots);
    }

    public function getTeamTwoCodeName(): string
    {
        return $this->teamTwoCodeName;
    }

    public function getTeamTwoPower(): int
    {
        return $this->teamTwoPower;
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

        return $this->buildTeamData($this->winningTeamId, $this->winningTeamName ?? 'Unknown Team', $this->winningTeamName ?? 'Unknown Team', []);
    }

    public function getTeamOneRobotIds(): array
    {
        return array_map(static fn (array $robot): int => (int) ($robot['id'] ?? 0), $this->teamOneRobots);
    }

    public function getTeamTwoRobotIds(): array
    {
        return array_map(static fn (array $robot): int => (int) ($robot['id'] ?? 0), $this->teamTwoRobots);
    }

    /**
     * @param list<array<string, mixed>>|null $robots
     */
    private function buildTeamData(int $id, string $name, string $codeName, ?array $robots): array
    {
        return [
            'id' => $id,
            'name' => $name,
            'codeName' => $codeName,
            'robots' => $robots ?? [],
        ];
    }
}
