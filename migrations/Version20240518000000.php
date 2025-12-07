<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240518000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add cached power totals to robot_dance_offs';
    }

    public function up(Schema $schema): void
    {
        $platform = $this->connection->getDatabasePlatform()->getName();

        if ($platform === 'mysql') {
            $this->addSql('ALTER TABLE robot_dance_offs ADD team_one_power INT NOT NULL DEFAULT 0, ADD team_two_power INT NOT NULL DEFAULT 0');
        } elseif ($platform === 'postgresql') {
            $this->addSql('ALTER TABLE robot_dance_offs ADD team_one_power INT NOT NULL DEFAULT 0');
            $this->addSql('ALTER TABLE robot_dance_offs ADD team_two_power INT NOT NULL DEFAULT 0');
        } else {
            throw new \RuntimeException(sprintf('Unsupported database platform "%s".', $platform));
        }
    }

    public function down(Schema $schema): void
    {
        $platform = $this->connection->getDatabasePlatform()->getName();

        if ($platform === 'mysql') {
            $this->addSql('ALTER TABLE robot_dance_offs DROP team_one_power, DROP team_two_power');
        } elseif ($platform === 'postgresql') {
            $this->addSql('ALTER TABLE robot_dance_offs DROP COLUMN team_one_power');
            $this->addSql('ALTER TABLE robot_dance_offs DROP COLUMN team_two_power');
        } else {
            throw new \RuntimeException(sprintf('Unsupported database platform "%s".', $platform));
        }
    }
}
