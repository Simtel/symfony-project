<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240418061808 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE
          config
        DROP
          INDEX UNIQ_D48A2F7CDE12AB56,
        ADD
          INDEX IDX_D48A2F7CDE12AB56 (created_by)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE
          config
        DROP
          INDEX IDX_D48A2F7CDE12AB56,
        ADD
          UNIQUE INDEX UNIQ_D48A2F7CDE12AB56 (created_by)');
    }
}
