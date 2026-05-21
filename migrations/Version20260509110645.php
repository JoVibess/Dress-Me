<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260509110645 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Align stores.user_id index name with Doctrine metadata';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE stores DROP FOREIGN KEY FK_4B2E153FA76ED395');
        $this->addSql('DROP INDEX IDX_4B2E153FA76ED395 ON stores');
        $this->addSql('CREATE INDEX IDX_D5907CCCA76ED395 ON stores (user_id)');
        $this->addSql('ALTER TABLE stores ADD CONSTRAINT FK_4B2E153FA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE stores DROP FOREIGN KEY FK_4B2E153FA76ED395');
        $this->addSql('DROP INDEX IDX_D5907CCCA76ED395 ON stores');
        $this->addSql('CREATE INDEX IDX_4B2E153FA76ED395 ON stores (user_id)');
        $this->addSql('ALTER TABLE stores ADD CONSTRAINT FK_4B2E153FA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
    }
}
