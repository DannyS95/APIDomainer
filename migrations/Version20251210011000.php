<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251210011000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Make teams stateful with composition metadata, drop origin battle concept, and rebuild robot_battle_view';
    }

    public function up(Schema $schema): void
    {
        $this->dropView();
        $this->removeOriginBattleColumns();
        $this->addTeamMetadataColumns();
        $this->recreateView();
    }

    public function down(Schema $schema): void
    {
        $this->dropView();
        $this->removeTeamMetadataColumns();
        $this->recreateLegacyView();
    }

    private function recreateView(): void
    {
        $this->addSql(<<<'SQL'
            CREATE VIEW robot_battle_view AS
            SELECT
                rdo.id AS battle_replay_id,
                rb.id AS battle_id,
                rdo.created_at,
                t1.id AS team_one_id,
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
                    WHERE tr.team_id = t1.id
                ), JSON_ARRAY()) AS team_one_robots,
                t2.id AS team_two_id,
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
                    WHERE tr.team_id = t2.id
                ), JSON_ARRAY()) AS team_two_robots,
                wt.id AS winning_team_id,
                wt.name AS winning_team_name
            FROM robot_dance_offs rdo
            INNER JOIN robot_battles rb ON rb.id = rdo.robot_battle_id
            INNER JOIN teams t1 ON t1.id = rdo.team_one_id
            INNER JOIN teams t2 ON t2.id = rdo.team_two_id
            LEFT JOIN teams wt ON wt.id = rdo.winning_team_id
        SQL);
    }

    private function recreateLegacyView(): void
    {
        $this->addSql(<<<'SQL'
            CREATE VIEW robot_battle_view AS
            SELECT
                rdo.id AS battle_replay_id,
                rb.id AS battle_id,
                rdo.created_at,
                t1.id AS team_one_id,
                t1.name AS team_one_name,
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
                    WHERE tr.team_id = t1.id
                ), JSON_ARRAY()) AS team_one_robots,
                t2.id AS team_two_id,
                t2.name AS team_two_name,
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
                    WHERE tr.team_id = t2.id
                ), JSON_ARRAY()) AS team_two_robots,
                wt.id AS winning_team_id,
                wt.name AS winning_team_name
            FROM robot_dance_offs rdo
            INNER JOIN robot_battles rb ON rb.id = rdo.robot_battle_id
            INNER JOIN teams t1 ON t1.id = rdo.team_one_id
            INNER JOIN teams t2 ON t2.id = rdo.team_two_id
            LEFT JOIN teams wt ON wt.id = rdo.winning_team_id
        SQL);
    }

    private function removeOriginBattleColumns(): void
    {
        $platform = $this->connection->getDatabasePlatform()->getName();

        if ($platform === 'postgresql') {
            $this->addSql('ALTER TABLE robot_battles DROP CONSTRAINT IF EXISTS FK_ROBOT_BATTLES_ORIGIN');
            $this->addSql('DROP INDEX IF EXISTS IDX_ROBOT_BATTLES_ORIGIN');
            $this->addSql('ALTER TABLE robot_battles DROP COLUMN IF EXISTS origin_battle_id');
        } else {
            // MySQL 8+ supports IF EXISTS on drop column; the FK/index were never added in the current schema.
            $this->addSql('ALTER TABLE robot_battles DROP COLUMN IF EXISTS origin_battle_id');
        }
    }

    private function addTeamMetadataColumns(): void
    {
        $this->addSql("ALTER TABLE teams ADD code_name VARCHAR(150) NOT NULL DEFAULT 'Legacy Team'");
        $this->addSql("ALTER TABLE teams ADD composition_signature VARCHAR(255) NOT NULL DEFAULT ''");
        $this->addSql('ALTER TABLE teams ADD robot_order JSON NOT NULL DEFAULT (JSON_ARRAY())');
    }

    private function removeTeamMetadataColumns(): void
    {
        $this->addSql('ALTER TABLE teams DROP COLUMN IF EXISTS code_name');
        $this->addSql('ALTER TABLE teams DROP COLUMN IF EXISTS composition_signature');
        $this->addSql('ALTER TABLE teams DROP COLUMN IF EXISTS robot_order');
    }

    private function dropView(): void
    {
        $this->addSql('DROP VIEW IF EXISTS robot_battle_view');
    }
}
