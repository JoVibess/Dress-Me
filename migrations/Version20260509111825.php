<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260509111825 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Align api_token.store_id index name with Doctrine metadata';
    }

    public function up(Schema $schema): void
    {
        $this->skipIf(
            !$schema->hasTable('api_token') || !$schema->getTable('api_token')->hasColumn('store_id'),
            'api_token.store_id does not exist yet.',
        );

        $this->addSql('ALTER TABLE api_token DROP FOREIGN KEY FK_7BA2F5EB48C4E0C');
        $this->addSql('DROP INDEX IDX_7BA2F5EB48C4E0C ON api_token');
        $this->addSql('CREATE INDEX IDX_7BA2F5EBB092A811 ON api_token (store_id)');
        $this->addSql('ALTER TABLE api_token ADD CONSTRAINT FK_7BA2F5EB48C4E0C FOREIGN KEY (store_id) REFERENCES stores (id)');
    }

    public function down(Schema $schema): void
    {
        $this->skipIf(
            !$schema->hasTable('api_token') || !$schema->getTable('api_token')->hasColumn('store_id'),
            'api_token.store_id does not exist.',
        );

        $this->addSql('ALTER TABLE api_token DROP FOREIGN KEY FK_7BA2F5EB48C4E0C');
        $this->addSql('DROP INDEX IDX_7BA2F5EBB092A811 ON api_token');
        $this->addSql('CREATE INDEX IDX_7BA2F5EB48C4E0C ON api_token (store_id)');
        $this->addSql('ALTER TABLE api_token ADD CONSTRAINT FK_7BA2F5EB48C4E0C FOREIGN KEY (store_id) REFERENCES stores (id)');
    }
}
