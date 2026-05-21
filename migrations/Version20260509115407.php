<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260509115407 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Align try_on_request.store_id index name with Doctrine metadata';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE try_on_request DROP FOREIGN KEY FK_6C7A60B9B092A811');
        $this->addSql('DROP INDEX IDX_6C7A60B9B092A811 ON try_on_request');
        $this->addSql('CREATE INDEX IDX_34C5AA91B092A811 ON try_on_request (store_id)');
        $this->addSql('ALTER TABLE try_on_request ADD CONSTRAINT FK_6C7A60B9B092A811 FOREIGN KEY (store_id) REFERENCES stores (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE try_on_request DROP FOREIGN KEY FK_6C7A60B9B092A811');
        $this->addSql('DROP INDEX IDX_34C5AA91B092A811 ON try_on_request');
        $this->addSql('CREATE INDEX IDX_6C7A60B9B092A811 ON try_on_request (store_id)');
        $this->addSql('ALTER TABLE try_on_request ADD CONSTRAINT FK_6C7A60B9B092A811 FOREIGN KEY (store_id) REFERENCES stores (id)');
    }
}
