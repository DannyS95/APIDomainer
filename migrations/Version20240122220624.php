<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240122220624 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE robots (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL,
                powermove VARCHAR(50) NOT NULL,
                experience INT NOT NULL,
                out_of_order BOOLEAN NOT NULL,
                avatar VARCHAR(2000) NOT NULL
            )
        ');

        $this->addSql("INSERT INTO robots (name, powermove, experience, out_of_order, avatar)
            VALUES
                ('Groovy Jane', 'Windmill Spin', 8, false, 'https://robohash.org/groovy-jane.png'),
                ('Breakbeat Billy', 'Backflip Kick', 7, true, 'https://robohash.org/breakbeat-billy.png'),
                ('Electric Eddie', 'Moonwalk', 10, false, 'https://robohash.org/electric-eddie.png'),
                ('Twistin\' Tina', 'Headspin', 6, true, 'https://robohash.org/twistin-tina.png'),
                ('Dynamic Dave', 'Flare Kick', 9, false, 'https://robohash.org/dynamic-dave.png'),
                ('Spinsational Sam', '360 Spin', 8, true, 'https://robohash.org/spinsational-sam.png'),
                ('Whirlwind Wendy', 'Corkscrew Twist', 7, false, 'https://robohash.org/whirlwind-wendy.png'),
                ('B-Boy Bobby', 'Freeze Frame', 5, true, 'https://robohash.org/b-boy-bobby.png'),
                ('Dizzy Daisy', 'Spiral Spin', 6, false, 'https://robohash.org/dizzy-daisy.png'),
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
        $this->addSql('CREATE INDEX out_of_order ON robots (out_of_order);');

        $this->addSql('CREATE TABLE dance_offs (
            robot_one VARCHAR(255) NOT NULL,
            robot_two VARCHAR(255) NOT NULL,
            winner VARCHAR(255) NOT NULL)
        ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP IF EXISTS robots');
        $this->addSql('DROP IF EXISTS dace_offs');
    }
}
