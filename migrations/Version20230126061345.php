<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230126061345 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add date range fields for User';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE
          user
        ADD
          blocked_start_date DATE DEFAULT NULL COMMENT \'(DC2Type:date_immutable)\',
        ADD
          blocked_end_date DATE DEFAULT NULL COMMENT \'(DC2Type:date_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user DROP blocked_start_date, DROP blocked_end_date');
    }
}
