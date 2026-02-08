<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260207000000 extends AbstractMigration
{
    private const CHECK_DISTINCT_TEAMS = 'CHK_RDO_DISTINCT_TEAMS';
    private const CHECK_WINNER_PARTICIPATION = 'CHK_RDO_WINNER_IS_PARTICIPANT';

    public function getDescription(): string
    {
        return 'Add dance-off integrity checks and rename teams tables to battle_* naming';
    }

    public function up(Schema $schema): void
    {
        $platform = $this->connection->getDatabasePlatform()->getName();

        $this->dropView();
        $this->addIntegrityConstraints($platform);
        $this->renameTeamTables($platform, true);
        $this->recreateViewUsingBattleTeamTables();
    }

    public function down(Schema $schema): void
    {
        $platform = $this->connection->getDatabasePlatform()->getName();

        $this->dropView();
        $this->renameTeamTables($platform, false);
        $this->dropIntegrityConstraints($platform);
        $this->recreateViewUsingLegacyTableNames();
    }

    private function addIntegrityConstraints(string $platform): void
    {
        if (!in_array($platform, ['mysql', 'postgresql'], true)) {
            throw new \RuntimeException(sprintf('Unsupported database platform "%s".', $platform));
        }

        $this->addSql(sprintf(
            'ALTER TABLE robot_dance_offs ADD CONSTRAINT %s CHECK (team_one_id <> team_two_id)',
            self::CHECK_DISTINCT_TEAMS
        ));
        $this->addSql(sprintf(
            'ALTER TABLE robot_dance_offs ADD CONSTRAINT %s CHECK (winning_team_id IS NULL OR winning_team_id = team_one_id OR winning_team_id = team_two_id)',
            self::CHECK_WINNER_PARTICIPATION
        ));
    }

    private function dropIntegrityConstraints(string $platform): void
    {
        if ($platform === 'mysql') {
            $this->addSql(sprintf('ALTER TABLE robot_dance_offs DROP CHECK %s', self::CHECK_WINNER_PARTICIPATION));
            $this->addSql(sprintf('ALTER TABLE robot_dance_offs DROP CHECK %s', self::CHECK_DISTINCT_TEAMS));

            return;
        }

        if ($platform === 'postgresql') {
            $this->addSql(sprintf('ALTER TABLE robot_dance_offs DROP CONSTRAINT IF EXISTS %s', self::CHECK_WINNER_PARTICIPATION));
            $this->addSql(sprintf('ALTER TABLE robot_dance_offs DROP CONSTRAINT IF EXISTS %s', self::CHECK_DISTINCT_TEAMS));

            return;
        }

        throw new \RuntimeException(sprintf('Unsupported database platform "%s".', $platform));
    }

    private function renameTeamTables(string $platform, bool $toBattleNames): void
    {
        if ($platform === 'mysql') {
            if ($toBattleNames) {
                $this->addSql('RENAME TABLE teams TO battle_teams');
                $this->addSql('RENAME TABLE team_robots TO battle_team_robots');
            } else {
                $this->addSql('RENAME TABLE battle_teams TO teams');
                $this->addSql('RENAME TABLE battle_team_robots TO team_robots');
            }

            return;
        }

        if ($platform === 'postgresql') {
            if ($toBattleNames) {
                $this->addSql('ALTER TABLE teams RENAME TO battle_teams');
                $this->addSql('ALTER TABLE team_robots RENAME TO battle_team_robots');
            } else {
                $this->addSql('ALTER TABLE battle_teams RENAME TO teams');
                $this->addSql('ALTER TABLE battle_team_robots RENAME TO team_robots');
            }

            return;
        }

        throw new \RuntimeException(sprintf('Unsupported database platform "%s".', $platform));
    }

    private function recreateViewUsingBattleTeamTables(): void
    {
        $this->addSql(<<<'SQL'
            CREATE VIEW robot_battle_view AS
            SELECT
                rdo.id AS battle_replay_id,
                rb.id AS battle_id,
                rdo.created_at,
                rdo.team_one_id,
                t1.name AS team_one_name,
                t1.code_name AS team_one_code_name,
                rdo.team_one_power,
                COALESCE((
                    SELECT JSON_ARRAYAGG(JSON_OBJECT(
                        'id', r.id,
                        'name', r.name,
                        'powermove', r.powermove,
                        'experience', r.experience,
                        'outOfOrder', r.out_of_order,
                        'avatar', r.avatar
                    ))
                    FROM battle_team_robots tr
                    INNER JOIN robots r ON r.id = tr.robot_id
                    WHERE tr.team_id = rdo.team_one_id
                ), JSON_ARRAY()) AS team_one_robots,
                rdo.team_two_id,
                t2.name AS team_two_name,
                t2.code_name AS team_two_code_name,
                rdo.team_two_power,
                COALESCE((
                    SELECT JSON_ARRAYAGG(JSON_OBJECT(
                        'id', r.id,
                        'name', r.name,
                        'powermove', r.powermove,
                        'experience', r.experience,
                        'outOfOrder', r.out_of_order,
                        'avatar', r.avatar
                    ))
                    FROM battle_team_robots tr
                    INNER JOIN robots r ON r.id = tr.robot_id
                    WHERE tr.team_id = rdo.team_two_id
                ), JSON_ARRAY()) AS team_two_robots,
                rdo.winning_team_id,
                wt.name AS winning_team_name
            FROM robot_dance_offs rdo
            INNER JOIN robot_battles rb ON rb.id = rdo.robot_battle_id
            INNER JOIN battle_teams t1 ON t1.id = rdo.team_one_id
            INNER JOIN battle_teams t2 ON t2.id = rdo.team_two_id
            LEFT JOIN battle_teams wt ON wt.id = rdo.winning_team_id
        SQL);
    }

    private function recreateViewUsingLegacyTableNames(): void
    {
        $this->addSql(<<<'SQL'
            CREATE VIEW robot_battle_view AS
            SELECT
                rdo.id AS battle_replay_id,
                rb.id AS battle_id,
                rdo.created_at,
                rdo.team_one_id,
                t1.name AS team_one_name,
                t1.code_name AS team_one_code_name,
                rdo.team_one_power,
                COALESCE((
                    SELECT JSON_ARRAYAGG(JSON_OBJECT(
                        'id', r.id,
                        'name', r.name,
                        'powermove', r.powermove,
                        'experience', r.experience,
                        'outOfOrder', r.out_of_order,
                        'avatar', r.avatar
                    ))
                    FROM team_robots tr
                    INNER JOIN robots r ON r.id = tr.robot_id
                    WHERE tr.team_id = rdo.team_one_id
                ), JSON_ARRAY()) AS team_one_robots,
                rdo.team_two_id,
                t2.name AS team_two_name,
                t2.code_name AS team_two_code_name,
                rdo.team_two_power,
                COALESCE((
                    SELECT JSON_ARRAYAGG(JSON_OBJECT(
                        'id', r.id,
                        'name', r.name,
                        'powermove', r.powermove,
                        'experience', r.experience,
                        'outOfOrder', r.out_of_order,
                        'avatar', r.avatar
                    ))
                    FROM team_robots tr
                    INNER JOIN robots r ON r.id = tr.robot_id
                    WHERE tr.team_id = rdo.team_two_id
                ), JSON_ARRAY()) AS team_two_robots,
                rdo.winning_team_id,
                wt.name AS winning_team_name
            FROM robot_dance_offs rdo
            INNER JOIN robot_battles rb ON rb.id = rdo.robot_battle_id
            INNER JOIN teams t1 ON t1.id = rdo.team_one_id
            INNER JOIN teams t2 ON t2.id = rdo.team_two_id
            LEFT JOIN teams wt ON wt.id = rdo.winning_team_id
        SQL);
    }

    private function dropView(): void
    {
        $this->addSql('DROP VIEW IF EXISTS robot_battle_view');
    }
}
