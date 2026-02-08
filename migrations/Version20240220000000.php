<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240220000000 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('DROP VIEW IF EXISTS robot_battle_view');
        $this->addSql(<<<'SQL'
            CREATE VIEW robot_battle_view AS
            SELECT
                rdo.id AS battle_id,
                rdo.created_at,
                rdo.team_one_id,
                t1.name AS team_one_name,
                COALESCE(
                    (SELECT JSON_ARRAYAGG(JSON_OBJECT(
                        'id', r.id,
                        'name', r.name,
                        'powermove', r.powermove,
                        'experience', r.experience,
                        'outOfOrder', r.out_of_order,
                        'avatar', r.avatar
                    ))
                    FROM team_robots tr
                    INNER JOIN robots r ON r.id = tr.robot_id
                    WHERE tr.team_id = rdo.team_one_id),
                    JSON_ARRAY()
                ) AS team_one_robots,
                rdo.team_two_id,
                t2.name AS team_two_name,
                COALESCE(
                    (SELECT JSON_ARRAYAGG(JSON_OBJECT(
                        'id', r.id,
                        'name', r.name,
                        'powermove', r.powermove,
                        'experience', r.experience,
                        'outOfOrder', r.out_of_order,
                        'avatar', r.avatar
                    ))
                    FROM team_robots tr
                    INNER JOIN robots r ON r.id = tr.robot_id
                    WHERE tr.team_id = rdo.team_two_id),
                    JSON_ARRAY()
                ) AS team_two_robots,
                rdo.winning_team_id,
                wt.name AS winning_team_name
            FROM robot_dance_offs rdo
            INNER JOIN teams t1 ON t1.id = rdo.team_one_id
            INNER JOIN teams t2 ON t2.id = rdo.team_two_id
            LEFT JOIN teams wt ON wt.id = rdo.winning_team_id
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP VIEW IF EXISTS robot_battle_view');
    }
}
