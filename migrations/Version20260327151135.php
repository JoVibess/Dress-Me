<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260327151135 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE subscription (id INT AUTO_INCREMENT NOT NULL, status VARCHAR(20) NOT NULL, starts_at DATETIME NOT NULL, ends_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, user_id INT NOT NULL, offer_id INT NOT NULL, INDEX IDX_A3C664D3A76ED395 (user_id), INDEX IDX_A3C664D353C674EE (offer_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE credit_batch (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(20) NOT NULL, initial_amount INT NOT NULL, remaining_amount INT NOT NULL, expires_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, subscription_id INT NOT NULL, INDEX IDX_A064DC7A9A1887DC (subscription_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE offer (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, price NUMERIC(10, 2) NOT NULL, type VARCHAR(50) NOT NULL, credit_amount INT NOT NULL, duration_in_months INT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE api_token (id INT AUTO_INCREMENT NOT NULL, token_value VARCHAR(255) NOT NULL, is_active TINYINT NOT NULL, activated_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, user_id INT NOT NULL, UNIQUE INDEX UNIQ_7BA2F5EBBEA95C75 (token_value), INDEX IDX_7BA2F5EBA76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, email VARCHAR(255) NOT NULL, roles JSON NOT NULL, website VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), UNIQUE INDEX UNIQ_USER_WEBSITE (website), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (queue_name, available_at, delivered_at, id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE subscription ADD CONSTRAINT FK_A3C664D3A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE subscription ADD CONSTRAINT FK_A3C664D353C674EE FOREIGN KEY (offer_id) REFERENCES offer (id)');
        $this->addSql('ALTER TABLE credit_batch ADD CONSTRAINT FK_A4117D9B9A1887DC FOREIGN KEY (subscription_id) REFERENCES subscription (id)');
        $this->addSql('ALTER TABLE api_token ADD CONSTRAINT FK_7BA2F5EBA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE subscription DROP FOREIGN KEY FK_A3C664D3A76ED395');
        $this->addSql('ALTER TABLE subscription DROP FOREIGN KEY FK_A3C664D353C674EE');
        $this->addSql('ALTER TABLE credit_batch DROP FOREIGN KEY FK_A4117D9B9A1887DC');
        $this->addSql('ALTER TABLE api_token DROP FOREIGN KEY FK_7BA2F5EBA76ED395');
        $this->addSql('DROP TABLE subscription');
        $this->addSql('DROP TABLE credit_batch');
        $this->addSql('DROP TABLE offer');
        $this->addSql('DROP TABLE api_token');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
