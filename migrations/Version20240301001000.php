<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240301001000 extends AbstractMigration
{
    private const ROBOT_ROWS = [
        ['name' => 'Laser Lou', 'powermove' => 'Photon Flip', 'experience' => 8, 'out_of_order' => false, 'avatar' => 'https://robohash.org/laser-lou.png'],
        ['name' => 'Matrix Mia', 'powermove' => 'Binary Breaker', 'experience' => 9, 'out_of_order' => false, 'avatar' => 'https://robohash.org/matrix-mia.png'],
        ['name' => 'Groove Greg', 'powermove' => 'Neon Nova', 'experience' => 7, 'out_of_order' => true, 'avatar' => 'https://robohash.org/groove-greg.png'],
        ['name' => 'Pixel Pam', 'powermove' => 'Raster Roll', 'experience' => 6, 'out_of_order' => false, 'avatar' => 'https://robohash.org/pixel-pam.png'],
        ['name' => 'Tempo Tom', 'powermove' => 'Beat Burst', 'experience' => 8, 'out_of_order' => true, 'avatar' => 'https://robohash.org/tempo-tom.png'],
    ];

    public function getDescription(): string
    {
        return 'Seed five additional robots to expand the roster.';
    }

    public function up(Schema $schema): void
    {
        foreach (self::ROBOT_ROWS as $row) {
            $this->addSql(
                'INSERT INTO robots (name, powermove, experience, out_of_order, avatar) VALUES (:name, :powermove, :experience, :out_of_order, :avatar)',
                [
                    'name' => $row['name'],
                    'powermove' => $row['powermove'],
                    'experience' => $row['experience'],
                    'out_of_order' => $row['out_of_order'],
                    'avatar' => $row['avatar'],
                ],
                [
                    'name' => \PDO::PARAM_STR,
                    'powermove' => \PDO::PARAM_STR,
                    'experience' => \PDO::PARAM_INT,
                    'out_of_order' => \PDO::PARAM_BOOL,
                    'avatar' => \PDO::PARAM_STR,
                ]
            );
        }
    }

    public function down(Schema $schema): void
    {
        $names = array_map(static fn (array $row): string => $row['name'], self::ROBOT_ROWS);
        $placeholders = implode(', ', array_fill(0, count($names), '?'));

        $this->addSql(
            sprintf('DELETE FROM robots WHERE name IN (%s)', $placeholders),
            $names,
            array_fill(0, count($names), \PDO::PARAM_STR)
        );
    }
}
