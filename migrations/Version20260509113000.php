<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260509113000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Move api tokens from users to stores';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE api_token ADD store_id INT DEFAULT NULL');
        $this->addSql('UPDATE api_token t INNER JOIN stores s ON s.user_id = t.user_id SET t.store_id = s.id');
        $this->addSql('ALTER TABLE api_token MODIFY store_id INT NOT NULL');
        $this->addSql('CREATE INDEX IDX_7BA2F5EBB092A811 ON api_token (store_id)');
        $this->addSql('ALTER TABLE api_token ADD CONSTRAINT FK_7BA2F5EB48C4E0C FOREIGN KEY (store_id) REFERENCES stores (id)');
        $this->addSql('ALTER TABLE api_token DROP FOREIGN KEY FK_7BA2F5EBA76ED395');
        $this->addSql('DROP INDEX IDX_7BA2F5EBA76ED395 ON api_token');
        $this->addSql('ALTER TABLE api_token DROP user_id');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE api_token ADD user_id INT DEFAULT NULL');
        $this->addSql('UPDATE api_token t INNER JOIN stores s ON s.id = t.store_id SET t.user_id = s.user_id');
        $this->addSql('ALTER TABLE api_token MODIFY user_id INT NOT NULL');
        $this->addSql('CREATE INDEX IDX_7BA2F5EBA76ED395 ON api_token (user_id)');
        $this->addSql('ALTER TABLE api_token ADD CONSTRAINT FK_7BA2F5EBA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE api_token DROP FOREIGN KEY FK_7BA2F5EB48C4E0C');
        $this->addSql('DROP INDEX IDX_7BA2F5EBB092A811 ON api_token');
        $this->addSql('ALTER TABLE api_token DROP store_id');
    }
}
