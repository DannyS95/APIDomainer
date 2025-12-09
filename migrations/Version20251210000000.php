<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251210000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Seed additional robots so IDs 11 and 12 exist for testing/replays';
    }

    public function up(Schema $schema): void
    {
        $platform = $this->connection->getDatabasePlatform()->getName();

        $robots = [
            [
                'id' => 11,
                'name' => 'Groove Guardian',
                'powermove' => 'Boom Box Bounce',
                'experience' => 7,
                'out_of_order' => 0,
                'avatar' => 'https://robohash.org/groove-guardian.png',
            ],
            [
                'id' => 12,
                'name' => 'Circuit Surfer',
                'powermove' => 'Neon Wave',
                'experience' => 8,
                'out_of_order' => 0,
                'avatar' => 'https://robohash.org/circuit-surfer.png',
            ],
            [
                'id' => 13,
                'name' => 'Pixel Popper',
                'powermove' => 'Glitch Glide',
                'experience' => 6,
                'out_of_order' => 0,
                'avatar' => 'https://robohash.org/pixel-popper.png',
            ],
            [
                'id' => 14,
                'name' => 'Voltage Vixen',
                'powermove' => 'Static Slide',
                'experience' => 7,
                'out_of_order' => 0,
                'avatar' => 'https://robohash.org/voltage-vixen.png',
            ],
            [
                'id' => 15,
                'name' => 'Laser Lenny',
                'powermove' => 'Photon Fling',
                'experience' => 9,
                'out_of_order' => 0,
                'avatar' => 'https://robohash.org/laser-lenny.png',
            ],
        ];

        foreach ($robots as $robot) {
            if ($platform === 'mysql') {
                $this->addSql(
                    "INSERT INTO robots (id, name, powermove, experience, out_of_order, avatar)
                     VALUES (:id, :name, :powermove, :experience, :out_of_order, :avatar)
                     ON DUPLICATE KEY UPDATE
                        name = VALUES(name),
                        powermove = VALUES(powermove),
                        experience = VALUES(experience),
                        out_of_order = VALUES(out_of_order),
                        avatar = VALUES(avatar)",
                    $robot
                );
            } elseif ($platform === 'postgresql') {
                $this->addSql(
                    "INSERT INTO robots (id, name, powermove, experience, out_of_order, avatar)
                     VALUES (:id, :name, :powermove, :experience, :out_of_order, :avatar)
                     ON CONFLICT (id) DO UPDATE SET
                        name = EXCLUDED.name,
                        powermove = EXCLUDED.powermove,
                        experience = EXCLUDED.experience,
                        out_of_order = EXCLUDED.out_of_order,
                        avatar = EXCLUDED.avatar",
                    $robot
                );
            } else {
                throw new \RuntimeException(sprintf('Unsupported database platform "%s".', $platform));
            }
        }
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DELETE FROM robots WHERE id IN (11, 12)');
    }
}
