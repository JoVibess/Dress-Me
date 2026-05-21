<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260509114357 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create try_on_request table for WordPress request traceability';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE try_on_request (id INT AUTO_INCREMENT NOT NULL, store_id INT NOT NULL, job_id VARCHAR(64) NOT NULL, anonymous_visitor_id VARCHAR(255) NOT NULL, site_url VARCHAR(255) NOT NULL, product_id INT NOT NULL, variation_id INT DEFAULT NULL, product_title VARCHAR(255) NOT NULL, product_description LONGTEXT DEFAULT NULL, product_image_url VARCHAR(2048) DEFAULT NULL, product_categories JSON NOT NULL, customer_image_provided TINYINT(1) NOT NULL, requested_anonymous_daily_quota INT DEFAULT NULL, status VARCHAR(20) NOT NULL, credits_consumed INT NOT NULL, error_code VARCHAR(50) DEFAULT NULL, error_message LONGTEXT DEFAULT NULL, completed_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_TRY_ON_JOB_ID (job_id), INDEX IDX_6C7A60B9B092A811 (store_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE try_on_request ADD CONSTRAINT FK_6C7A60B9B092A811 FOREIGN KEY (store_id) REFERENCES stores (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE try_on_request DROP FOREIGN KEY FK_6C7A60B9B092A811');
        $this->addSql('DROP TABLE try_on_request');
    }
}
