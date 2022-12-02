<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20221202072445 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add update at field to config table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE config ADD update_at DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE config DROP update_at');
    }
}
