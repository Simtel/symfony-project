<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20221229060945 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create log table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE log (
          id INT AUTO_INCREMENT NOT NULL,
          created_by INT DEFAULT NULL,
          action LONGTEXT NOT NULL,
          created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
          UNIQUE INDEX UNIQ_8F3F68C5DE12AB56 (created_by),
          PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE
          log
        ADD
          CONSTRAINT FK_8F3F68C5DE12AB56 FOREIGN KEY (created_by) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE log DROP FOREIGN KEY FK_8F3F68C5DE12AB56');
        $this->addSql('DROP TABLE log');
    }
}
