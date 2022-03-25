<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211028100634 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE aipd_analyse_avis (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', analyse_impact_referent_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', analyse_impact_dpd_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', analyse_impact_representant_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', analyse_impact_responsable_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', reponse VARCHAR(255) NOT NULL, date DATE DEFAULT NULL, detail VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_7356A4C6DE40EC4C (analyse_impact_referent_id), UNIQUE INDEX UNIQ_7356A4C61FDB1D73 (analyse_impact_dpd_id), UNIQUE INDEX UNIQ_7356A4C679599365 (analyse_impact_representant_id), UNIQUE INDEX UNIQ_7356A4C69A29412F (analyse_impact_responsable_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE aipd_analyse_avis ADD CONSTRAINT FK_7356A4C6DE40EC4C FOREIGN KEY (analyse_impact_referent_id) REFERENCES aipd_analyse_impact (id)');
        $this->addSql('ALTER TABLE aipd_analyse_avis ADD CONSTRAINT FK_7356A4C61FDB1D73 FOREIGN KEY (analyse_impact_dpd_id) REFERENCES aipd_analyse_impact (id)');
        $this->addSql('ALTER TABLE aipd_analyse_avis ADD CONSTRAINT FK_7356A4C679599365 FOREIGN KEY (analyse_impact_representant_id) REFERENCES aipd_analyse_impact (id)');
        $this->addSql('ALTER TABLE aipd_analyse_avis ADD CONSTRAINT FK_7356A4C69A29412F FOREIGN KEY (analyse_impact_responsable_id) REFERENCES aipd_analyse_impact (id)');
        $this->addSql('ALTER TABLE aipd_analyse_impact ADD avis_referent_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', ADD avis_dpd_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', ADD avis_representant_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', ADD avis_responsable_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', ADD ready_validation TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE aipd_analyse_impact ADD CONSTRAINT FK_DDAAB0246A0D339A FOREIGN KEY (avis_referent_id) REFERENCES aipd_analyse_avis (id)');
        $this->addSql('ALTER TABLE aipd_analyse_impact ADD CONSTRAINT FK_DDAAB02441FBE4EC FOREIGN KEY (avis_dpd_id) REFERENCES aipd_analyse_avis (id)');
        $this->addSql('ALTER TABLE aipd_analyse_impact ADD CONSTRAINT FK_DDAAB024756841F0 FOREIGN KEY (avis_representant_id) REFERENCES aipd_analyse_avis (id)');
        $this->addSql('ALTER TABLE aipd_analyse_impact ADD CONSTRAINT FK_DDAAB0241DE0ECE4 FOREIGN KEY (avis_responsable_id) REFERENCES aipd_analyse_avis (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_DDAAB0246A0D339A ON aipd_analyse_impact (avis_referent_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_DDAAB02441FBE4EC ON aipd_analyse_impact (avis_dpd_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_DDAAB024756841F0 ON aipd_analyse_impact (avis_representant_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_DDAAB0241DE0ECE4 ON aipd_analyse_impact (avis_responsable_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE aipd_analyse_impact DROP FOREIGN KEY FK_DDAAB0246A0D339A');
        $this->addSql('ALTER TABLE aipd_analyse_impact DROP FOREIGN KEY FK_DDAAB02441FBE4EC');
        $this->addSql('ALTER TABLE aipd_analyse_impact DROP FOREIGN KEY FK_DDAAB024756841F0');
        $this->addSql('ALTER TABLE aipd_analyse_impact DROP FOREIGN KEY FK_DDAAB0241DE0ECE4');
        $this->addSql('DROP TABLE aipd_analyse_avis');
        $this->addSql('DROP INDEX UNIQ_DDAAB0246A0D339A ON aipd_analyse_impact');
        $this->addSql('DROP INDEX UNIQ_DDAAB02441FBE4EC ON aipd_analyse_impact');
        $this->addSql('DROP INDEX UNIQ_DDAAB024756841F0 ON aipd_analyse_impact');
        $this->addSql('DROP INDEX UNIQ_DDAAB0241DE0ECE4 ON aipd_analyse_impact');
        $this->addSql('ALTER TABLE aipd_analyse_impact DROP avis_referent_id, DROP avis_dpd_id, DROP avis_representant_id, DROP avis_responsable_id, DROP ready_validation');
    }
}
