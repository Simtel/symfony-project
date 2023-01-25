<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230125082659 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add secret key field to user';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user ADD secret_key VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user DROP secret_key');
    }
}
