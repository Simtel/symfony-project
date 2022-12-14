<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20221214073322 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create created by filed to config table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE config ADD created_by INT DEFAULT NULL');
        $this->addSql('ALTER TABLE
          config
        ADD
          CONSTRAINT FK_D48A2F7CDE12AB56 FOREIGN KEY (created_by) REFERENCES user (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D48A2F7CDE12AB56 ON config (created_by)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE config DROP FOREIGN KEY FK_D48A2F7CDE12AB56');
        $this->addSql('DROP INDEX UNIQ_D48A2F7CDE12AB56 ON config');
        $this->addSql('ALTER TABLE config DROP created_by');
    }
}
