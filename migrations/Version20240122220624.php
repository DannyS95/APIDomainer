<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240122220624 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // -- ROBOTS TABLE --
        $this->addSql('CREATE TABLE robots (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            powermove VARCHAR(50) NOT NULL,
            experience INT NOT NULL,
            out_of_order BOOLEAN NOT NULL,
            avatar VARCHAR(2000) NOT NULL
        )');

        $this->addSql("INSERT INTO robots (name, powermove, experience, out_of_order, avatar)
            VALUES
                ('SpinMaster Steve', 'Tornado Twist', 7, true, 'https://robohash.org/spinmaster-steve.png'),
                ('Whirling Wilma', 'Helicopter Hop', 9, false, 'https://robohash.org/whirling-wilma.png'),
                ('Funky Fred', 'Funky Flare', 8, true, 'https://robohash.org/funky-fred.png'),
                ('Breakdance Betty', 'Spin Kick', 6, false, 'https://robohash.org/breakdance-betty.png'),
                ('Whiz Kid Willie', 'Whirlwind Whiz', 7, true, 'https://robohash.org/whiz-kid-willie.png'),
                ('Jitterbug Jenny', 'Jittery Jump', 5, false, 'https://robohash.org/jitterbug-jenny.png'),
                ('Dance Dynamo Doug', 'Dynamic Dance', 8, true, 'https://robohash.org/dance-dynamo-doug.png'),
                ('Spinorama Sally', 'Spinning Star', 9, false, 'https://robohash.org/spinorama-sally.png'),
                ('Rhythmic Rob', 'Rhythm Twist', 7, true, 'https://robohash.org/rhythmic-rob.png'),
                ('Twirling Tim', 'Twirl Time', 6, false, 'https://robohash.org/twirling-tim.png');
        ");

        $this->addSql('CREATE INDEX idx_name ON robots (name);');
        $this->addSql('CREATE INDEX idx_powermove ON robots (powermove);');
        $this->addSql('CREATE INDEX idx_experience ON robots (experience);');
        $this->addSql('CREATE INDEX idx_out_of_order ON robots (out_of_order);');

        // -- DANCE-OFFS TABLE --
        $this->addSql('CREATE TABLE robot_dance_offs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            winner_id INT DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (winner_id) REFERENCES robots(id) ON DELETE SET NULL
        )');

        $this->addSql('CREATE INDEX idx_winner ON robot_dance_offs (winner_id);');

        // -- PARTICIPANTS PIVOT TABLE --
        $this->addSql('CREATE TABLE robot_dance_off_participants (
            id INT AUTO_INCREMENT PRIMARY KEY,
            dance_off_id INT NOT NULL,
            robot_id INT NOT NULL,
            team VARCHAR(10) NOT NULL CHECK (team IN (\'teamOne\', \'teamTwo\')),
            FOREIGN KEY (dance_off_id) REFERENCES robot_dance_offs(id) ON DELETE CASCADE,
            FOREIGN KEY (robot_id) REFERENCES robots(id) ON DELETE CASCADE
        )');

        $this->addSql('CREATE INDEX idx_dance_off_id ON robot_dance_off_participants (dance_off_id);');
        $this->addSql('CREATE INDEX idx_robot_id ON robot_dance_off_participants (robot_id);');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE IF EXISTS robot_dance_off_participants');
        $this->addSql('DROP TABLE IF EXISTS robot_dance_offs');
        $this->addSql('DROP TABLE IF EXISTS robots');
    }
}
