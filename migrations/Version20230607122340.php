<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230607122340 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE config DROP FOREIGN KEY FK_D48A2F7CDE12AB56');
        $this->addSql('ALTER TABLE
          config
        ADD
          CONSTRAINT FK_D48A2F7CDE12AB56 FOREIGN KEY (created_by) REFERENCES user (id) ON UPDATE CASCADE ON DELETE CASCADE');


        $this->addSql('ALTER TABLE user_location DROP FOREIGN KEY FK_BE136DCBA76ED395');
        $this->addSql('ALTER TABLE user_location DROP FOREIGN KEY FK_BE136DCB64D218E');
        $this->addSql('ALTER TABLE
          user_location
        ADD
          CONSTRAINT FK_BE136DCB64D218E FOREIGN KEY (location_id) REFERENCES location (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE
          user_location
        ADD
          CONSTRAINT FK_BE136DCBA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE CASCADE ON DELETE CASCADE');

        $this->addSql('ALTER TABLE log DROP FOREIGN KEY FK_8F3F68C5DE12AB56');
        $this->addSql('ALTER TABLE
          log
        ADD
          CONSTRAINT FK_8F3F68C5DE12AB56 FOREIGN KEY (created_by) REFERENCES user (id) ON UPDATE CASCADE ON DELETE CASCADE');

        $this->addSql('ALTER TABLE contact DROP FOREIGN KEY FK_4C62E638A76ED395');
        $this->addSql('ALTER TABLE
          contact
        ADD
          CONSTRAINT FK_4C62E638A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE CASCADE ON DELETE CASCADE');

    }

    public function down(Schema $schema): void
    {

    }
}
