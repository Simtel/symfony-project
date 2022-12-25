<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20221225073747 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create location table with relation for user';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(
            'CREATE TABLE location (
          id INT AUTO_INCREMENT NOT NULL,
          name VARCHAR(255) NOT NULL,
          PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB'
        );
        $this->addSql(
            'CREATE TABLE user_location (
          user_id INT NOT NULL,
          location_id INT NOT NULL,
          INDEX IDX_BE136DCBA76ED395 (user_id),
          INDEX IDX_BE136DCB64D218E (location_id),
          PRIMARY KEY(user_id, location_id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB'
        );
        $this->addSql(
            'ALTER TABLE
          user_location
        ADD
          CONSTRAINT FK_BE136DCBA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)'
        );
        $this->addSql(
            'ALTER TABLE
          user_location
        ADD
          CONSTRAINT FK_BE136DCB64D218E FOREIGN KEY (location_id) REFERENCES location (id)'
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user_location DROP FOREIGN KEY FK_BE136DCBA76ED395');
        $this->addSql('ALTER TABLE user_location DROP FOREIGN KEY FK_BE136DCB64D218E');
        $this->addSql('DROP TABLE location');
        $this->addSql('DROP TABLE user_location');
    }
}
