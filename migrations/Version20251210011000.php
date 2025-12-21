<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251210011000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add origin_battle_id to robot_battles and expose it via robot_battle_view';
    }

    public function up(Schema $schema): void
    {
        $platform = $this->connection->getDatabasePlatform()->getName();

        if (!\in_array($platform, ['mysql', 'postgresql'], true)) {
            throw new \RuntimeException(sprintf('Unsupported database platform "%s".', $platform));
        }

        if ($platform === 'mysql') {
            $this->addSql('ALTER TABLE robot_battles ADD origin_battle_id INT DEFAULT NULL');
            $this->addSql('ALTER TABLE robot_battles ADD CONSTRAINT FK_ROBOT_BATTLES_ORIGIN FOREIGN KEY (origin_battle_id) REFERENCES robot_battles (id) ON DELETE SET NULL');
            $this->addSql('CREATE INDEX IDX_ROBOT_BATTLES_ORIGIN ON robot_battles (origin_battle_id)');
        } else {
            $this->addSql('ALTER TABLE robot_battles ADD origin_battle_id INT DEFAULT NULL');
            $this->addSql('ALTER TABLE robot_battles ADD CONSTRAINT FK_ROBOT_BATTLES_ORIGIN FOREIGN KEY (origin_battle_id) REFERENCES robot_battles (id) ON DELETE SET NULL');
            $this->addSql('CREATE INDEX IDX_ROBOT_BATTLES_ORIGIN ON robot_battles (origin_battle_id)');
        }

        $this->recreateView();
    }

    public function down(Schema $schema): void
    {
        if (!\in_array($this->connection->getDatabasePlatform()->getName(), ['mysql', 'postgresql'], true)) {
            throw new \RuntimeException(sprintf('Unsupported database platform "%s".', $this->connection->getDatabasePlatform()->getName()));
        }

        $this->addSql('DROP VIEW IF EXISTS robot_battle_view');
        $this->addSql('DROP INDEX IF EXISTS IDX_ROBOT_BATTLES_ORIGIN ON robot_battles');
        $this->addSql('ALTER TABLE robot_battles DROP FOREIGN KEY FK_ROBOT_BATTLES_ORIGIN');
        $this->addSql('ALTER TABLE robot_battles DROP COLUMN origin_battle_id');
        $this->recreateViewLegacy();
    }

    private function recreateView(): void
    {
        $this->addSql('DROP VIEW IF EXISTS robot_battle_view');
        $this->addSql(<<<'SQL'
            CREATE VIEW robot_battle_view AS
            SELECT
                rdo.id AS battle_replay_id,
                rb.id AS battle_id,
                rb.origin_battle_id,
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

    private function recreateViewLegacy(): void
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
}
