<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20221203074025 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Fix type update at config field';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(
            'ALTER TABLE
          config
        CHANGE
          update_at update_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\''
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql(
            'ALTER TABLE config CHANGE update_at update_at DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\''
        );
    }
}
