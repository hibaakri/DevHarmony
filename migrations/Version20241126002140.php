<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241126002140 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE produit DROP FOREIGN KEY FK_29A5EC2712469DE2');
        $this->addSql('ALTER TABLE produit DROP FOREIGN KEY FK_29A5EC27EAB5DEB');
        $this->addSql('DROP TABLE produit');
        $this->addSql('ALTER TABLE panier ADD panier_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE panier ADD CONSTRAINT FK_24CC0DF2F77D927C FOREIGN KEY (panier_id) REFERENCES panier (id)');
        $this->addSql('CREATE INDEX IDX_24CC0DF2F77D927C ON panier (panier_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE produit (id INT AUTO_INCREMENT NOT NULL, category_id INT DEFAULT NULL, many_to_one_id INT DEFAULT NULL, titre VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, description VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, prix INT NOT NULL, created_at DATE NOT NULL, INDEX IDX_29A5EC2712469DE2 (category_id), INDEX IDX_29A5EC27EAB5DEB (many_to_one_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE produit ADD CONSTRAINT FK_29A5EC2712469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE produit ADD CONSTRAINT FK_29A5EC27EAB5DEB FOREIGN KEY (many_to_one_id) REFERENCES panier (id)');
        $this->addSql('ALTER TABLE panier DROP FOREIGN KEY FK_24CC0DF2F77D927C');
        $this->addSql('DROP INDEX IDX_24CC0DF2F77D927C ON panier');
        $this->addSql('ALTER TABLE panier DROP panier_id');
    }
}
