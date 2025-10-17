<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240301000000 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('DROP VIEW IF EXISTS robot_battle_view');

        $platform = $this->connection->getDatabasePlatform()->getName();

        if ($platform === 'mysql') {
            $this->addSql('CREATE TABLE robot_battles (id INT AUTO_INCREMENT NOT NULL, created_at DATETIME NOT NULL COMMENT \'
                (DC2Type:datetime_immutable)  \', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
            $this->addSql('ALTER TABLE robot_dance_offs ADD robot_battle_id INT DEFAULT NULL');
            $this->addSql('CREATE INDEX IDX_ROBOT_DANCE_OFFS_BATTLE ON robot_dance_offs (robot_battle_id)');
            $this->addSql('INSERT INTO robot_battles (id, created_at) SELECT id, created_at FROM robot_dance_offs');
            $this->addSql('UPDATE robot_dance_offs SET robot_battle_id = id');
            $this->addSql('ALTER TABLE robot_dance_offs MODIFY robot_battle_id INT NOT NULL');
            $this->addSql('ALTER TABLE robot_dance_offs ADD CONSTRAINT FK_ROBOT_DANCE_OFFS_BATTLE FOREIGN KEY (robot_battle_id) REFERENCES robot_battles (id) ON DELETE CASCADE');
        } elseif ($platform === 'postgresql') {
            $this->addSql('CREATE TABLE robot_battles (id SERIAL NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
            $this->addSql('ALTER TABLE robot_dance_offs ADD robot_battle_id INT DEFAULT NULL');
            $this->addSql('CREATE INDEX IDX_ROBOT_DANCE_OFFS_BATTLE ON robot_dance_offs (robot_battle_id)');
            $this->addSql('INSERT INTO robot_battles (id, created_at) SELECT id, created_at FROM robot_dance_offs');
            $this->addSql('SELECT setval(\'robot_battles_id_seq\', COALESCE((SELECT MAX(id) FROM robot_battles), 1))');
            $this->addSql('UPDATE robot_dance_offs SET robot_battle_id = id');
            $this->addSql('ALTER TABLE robot_dance_offs ALTER COLUMN robot_battle_id SET NOT NULL');
            $this->addSql('ALTER TABLE robot_dance_offs ADD CONSTRAINT FK_ROBOT_DANCE_OFFS_BATTLE FOREIGN KEY (robot_battle_id) REFERENCES robot_battles (id) ON DELETE CASCADE');
        } else {
            throw new \RuntimeException(sprintf('Unsupported database platform "%s".', $platform));
        }

        $this->addSql(<<<'SQL'
            CREATE VIEW robot_battle_view AS
            SELECT
                rdo.id AS dance_off_id,
                rb.id AS battle_id,
                rdo.created_at,
                t1.id AS team_one_id,
                t1.name AS team_one_name,
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

    public function down(Schema $schema): void
    {
        $platform = $this->connection->getDatabasePlatform()->getName();

        $this->addSql('DROP VIEW IF EXISTS robot_battle_view');

        if ($platform === 'mysql') {
            $this->addSql('ALTER TABLE robot_dance_offs DROP FOREIGN KEY FK_ROBOT_DANCE_OFFS_BATTLE');
            $this->addSql('DROP INDEX IDX_ROBOT_DANCE_OFFS_BATTLE ON robot_dance_offs');
            $this->addSql('ALTER TABLE robot_dance_offs DROP COLUMN robot_battle_id');
            $this->addSql('DROP TABLE robot_battles');
        } elseif ($platform === 'postgresql') {
            $this->addSql('ALTER TABLE robot_dance_offs DROP CONSTRAINT FK_ROBOT_DANCE_OFFS_BATTLE');
            $this->addSql('DROP INDEX IF EXISTS IDX_ROBOT_DANCE_OFFS_BATTLE');
            $this->addSql('ALTER TABLE robot_dance_offs DROP COLUMN robot_battle_id');
            $this->addSql('DROP TABLE robot_battles');
        } else {
            throw new \RuntimeException(sprintf('Unsupported database platform "%s".', $platform));
        }

        $this->addSql(<<<'SQL'
            CREATE VIEW robot_battle_view AS
            SELECT
                rdo.id AS battle_id,
                rdo.created_at,
                t1.id AS team_one_id,
                t1.name AS team_one_name,
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
            INNER JOIN teams t1 ON t1.id = rdo.team_one_id
            INNER JOIN teams t2 ON t2.id = rdo.team_two_id
            LEFT JOIN teams wt ON wt.id = rdo.winning_team_id
        SQL);
    }
}
