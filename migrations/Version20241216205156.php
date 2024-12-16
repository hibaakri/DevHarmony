<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241216205156 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE avis DROP FOREIGN KEY FK_8F91ABF0727ACA70');
        $this->addSql('DROP INDEX IDX_8F91ABF0727ACA70 ON avis');
        $this->addSql('ALTER TABLE avis ADD note INT NOT NULL, ADD visibilite TINYINT(1) NOT NULL, DROP parent_id, CHANGE produit_id produit_id INT DEFAULT NULL, CHANGE reponse reponse VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE service_apres_vente DROP FOREIGN KEY FK_E8A0B369F0B5AF0B');
        $this->addSql('DROP INDEX IDX_E8A0B369F0B5AF0B ON service_apres_vente');
        $this->addSql('ALTER TABLE service_apres_vente DROP createdby_id');
        $this->addSql('ALTER TABLE user ADD reset_token VARCHAR(100) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `user` DROP reset_token');
        $this->addSql('ALTER TABLE service_apres_vente ADD createdby_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE service_apres_vente ADD CONSTRAINT FK_E8A0B369F0B5AF0B FOREIGN KEY (createdby_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_E8A0B369F0B5AF0B ON service_apres_vente (createdby_id)');
        $this->addSql('ALTER TABLE avis ADD parent_id INT DEFAULT NULL, DROP note, DROP visibilite, CHANGE produit_id produit_id INT NOT NULL, CHANGE reponse reponse VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE avis ADD CONSTRAINT FK_8F91ABF0727ACA70 FOREIGN KEY (parent_id) REFERENCES avis (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_8F91ABF0727ACA70 ON avis (parent_id)');
    }
}
