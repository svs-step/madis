<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200515122603 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE conformite_traitement (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', traitement_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', nb_conformes SMALLINT NOT NULL, nb_non_conformes_mineures SMALLINT NOT NULL, nb_non_conformes_majeures SMALLINT NOT NULL, UNIQUE INDEX UNIQ_85B1C39CDDA344B6 (traitement_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE conformite_traitement_reponse (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', question_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', conformite_traitement_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', conforme TINYINT(1) NOT NULL, INDEX IDX_6B4E4201E27F6BF (question_id), INDEX IDX_6B4E420CEF983B6 (conformite_traitement_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE conformite_traitement_reponse_action_protection (reponse_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', mesurement_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', INDEX IDX_A4EBC0E4CF18BB82 (reponse_id), INDEX IDX_A4EBC0E42EA38911 (mesurement_id), PRIMARY KEY(reponse_id, mesurement_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE conformite_traitement_question (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', question LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE conformite_traitement ADD CONSTRAINT FK_85B1C39CDDA344B6 FOREIGN KEY (traitement_id) REFERENCES registry_treatment (id)');
        $this->addSql('ALTER TABLE conformite_traitement_reponse ADD CONSTRAINT FK_6B4E4201E27F6BF FOREIGN KEY (question_id) REFERENCES conformite_traitement_question (id)');
        $this->addSql('ALTER TABLE conformite_traitement_reponse ADD CONSTRAINT FK_6B4E420CEF983B6 FOREIGN KEY (conformite_traitement_id) REFERENCES conformite_traitement (id)');
        $this->addSql('ALTER TABLE conformite_traitement_reponse_action_protection ADD CONSTRAINT FK_A4EBC0E4CF18BB82 FOREIGN KEY (reponse_id) REFERENCES conformite_traitement_reponse (id)');
        $this->addSql('ALTER TABLE conformite_traitement_reponse_action_protection ADD CONSTRAINT FK_A4EBC0E42EA38911 FOREIGN KEY (mesurement_id) REFERENCES registry_mesurement (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE conformite_traitement_reponse DROP FOREIGN KEY FK_6B4E420CEF983B6');
        $this->addSql('ALTER TABLE conformite_traitement_reponse_action_protection DROP FOREIGN KEY FK_A4EBC0E4CF18BB82');
        $this->addSql('ALTER TABLE conformite_traitement_reponse DROP FOREIGN KEY FK_6B4E4201E27F6BF');
        $this->addSql('DROP TABLE conformite_traitement');
        $this->addSql('DROP TABLE conformite_traitement_reponse');
        $this->addSql('DROP TABLE conformite_traitement_reponse_action_protection');
        $this->addSql('DROP TABLE conformite_traitement_question');
    }
}
