<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230127062855 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add contacts table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE contact (
          id INT AUTO_INCREMENT NOT NULL,
          user_id INT DEFAULT NULL,
          code VARCHAR(180) NOT NULL,
          name VARCHAR(180) NOT NULL,
          value VARCHAR(180) DEFAULT NULL,
          INDEX IDX_4C62E638A76ED395 (user_id),
          PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE
          contact
        ADD
          CONSTRAINT FK_4C62E638A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE contact DROP FOREIGN KEY FK_4C62E638A76ED395');
        $this->addSql('DROP TABLE contact');
    }
}
