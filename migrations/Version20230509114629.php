<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230509114629 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reset_password_request DROP FOREIGN KEY FK_7CE748AA76ED395');
        $this->addSql('DROP TABLE reset_password_request');
        $this->addSql('ALTER TABLE medicament DROP FOREIGN KEY medicament_ibfk_1');
        $this->addSql('DROP INDEX categorie ON medicament');
        $this->addSql('ALTER TABLE medicament CHANGE categorie categorie_id INT NOT NULL');
        $this->addSql('ALTER TABLE medicament ADD CONSTRAINT FK_9A9C723ABCF5E72D FOREIGN KEY (categorie_id) REFERENCES categorie (id)');
        $this->addSql('CREATE INDEX IDX_9A9C723ABCF5E72D ON medicament (categorie_id)');
        $this->addSql('DROP INDEX id_consultation_id ON ordonnance');
        $this->addSql('DROP INDEX dose ON ordonnance');
        $this->addSql('DROP INDEX id_consultation_id_2 ON ordonnance');
        $this->addSql('ALTER TABLE ordonnance DROP Nom_Medicament');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE reset_password_request (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, selector VARCHAR(20) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, hashed_token VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, requested_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', expires_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_7CE748AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE medicament DROP FOREIGN KEY FK_9A9C723ABCF5E72D');
        $this->addSql('DROP INDEX IDX_9A9C723ABCF5E72D ON medicament');
        $this->addSql('ALTER TABLE medicament CHANGE categorie_id categorie INT NOT NULL');
        $this->addSql('ALTER TABLE medicament ADD CONSTRAINT medicament_ibfk_1 FOREIGN KEY (categorie) REFERENCES categorie (id)');
        $this->addSql('CREATE INDEX categorie ON medicament (categorie)');
        $this->addSql('ALTER TABLE ordonnance ADD Nom_Medicament VARCHAR(255) NOT NULL');
        $this->addSql('CREATE INDEX id_consultation_id ON ordonnance (id_consultation_id)');
        $this->addSql('CREATE INDEX dose ON ordonnance (dose)');
        $this->addSql('CREATE INDEX id_consultation_id_2 ON ordonnance (id_consultation_id)');
    }
}
