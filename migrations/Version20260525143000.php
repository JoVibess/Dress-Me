<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260525143000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add try-on asset storage and generation tracking fields';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE try_on_request ADD customer_image_path VARCHAR(2048) DEFAULT NULL, ADD provider_request_id VARCHAR(255) DEFAULT NULL, ADD generated_image_path VARCHAR(2048) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE try_on_request DROP customer_image_path, DROP provider_request_id, DROP generated_image_path');
    }
}
