<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241126123714 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE whishliste ADD user_id INT NOT NULL');
        $this->addSql('ALTER TABLE whishliste ADD CONSTRAINT FK_DCF7F4D3A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('CREATE INDEX IDX_DCF7F4D3A76ED395 ON whishliste (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE whishliste DROP FOREIGN KEY FK_DCF7F4D3A76ED395');
        $this->addSql('DROP INDEX IDX_DCF7F4D3A76ED395 ON whishliste');
        $this->addSql('ALTER TABLE whishliste DROP user_id');
    }
}
