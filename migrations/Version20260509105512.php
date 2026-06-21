<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260509105512 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Introduce stores linked to users and backfill one store per existing user';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE stores (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, name VARCHAR(150) NOT NULL, website VARCHAR(255) NOT NULL, is_active TINYINT(1) NOT NULL, anonymous_daily_quota INT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_STORE_WEBSITE (website), INDEX IDX_4B2E153FA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE stores ADD CONSTRAINT FK_4B2E153FA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('INSERT INTO stores (user_id, name, website, is_active, anonymous_daily_quota, created_at, updated_at)
            SELECT id, name, website, 1, 20, created_at, updated_at
            FROM users');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE stores DROP FOREIGN KEY FK_4B2E153FA76ED395');
        $this->addSql('DROP TABLE stores');
    }
}
