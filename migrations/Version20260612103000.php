<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260612103000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add API secret to API tokens for signed WordPress requests';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE api_token ADD secret_value VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE api_token DROP secret_value');
    }
}
