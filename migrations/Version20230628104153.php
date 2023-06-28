<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230628104153 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE log DROP INDEX UNIQ_8F3F68C5DE12AB56, ADD INDEX IDX_8F3F68C5DE12AB56 (created_by)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE
          log
        DROP
          INDEX IDX_8F3F68C5DE12AB56,
        ADD
          UNIQUE INDEX UNIQ_8F3F68C5DE12AB56 (created_by)');
    }
}
