<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211006144910 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE aipd_critere_principe_fondamental (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', analyse_impact_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', modele_analyse_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', label VARCHAR(255) NOT NULL, label_livrable VARCHAR(255) NOT NULL, reponse VARCHAR(255) NOT NULL, visible TINYINT(1) NOT NULL, texte_conformite VARCHAR(255) NOT NULL, texte_non_conformite VARCHAR(255) NOT NULL, non_applicable VARCHAR(255) NOT NULL, justification VARCHAR(255) NOT NULL, INDEX IDX_332F007498AFC31 (analyse_impact_id), INDEX IDX_332F007EDA9114E (modele_analyse_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE aipd_critere_principe_fondamental ADD CONSTRAINT FK_332F007498AFC31 FOREIGN KEY (analyse_impact_id) REFERENCES aipd_analyse_impact (id)');
        $this->addSql('ALTER TABLE aipd_critere_principe_fondamental ADD CONSTRAINT FK_332F007EDA9114E FOREIGN KEY (modele_analyse_id) REFERENCES aipd_modele (id)');
        $this->addSql('DROP TABLE aipd_analyse_critere_principe_fondamental');
        $this->addSql('DROP TABLE aipd_modele_critere_principe_fondamental');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE aipd_analyse_critere_principe_fondamental (id CHAR(36) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:uuid)\', analyse_impact_id CHAR(36) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:uuid)\', label VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, label_livrable VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, reponse VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, visible TINYINT(1) NOT NULL, texte_conformite VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, texte_non_conformite VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, non_applicable VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, justification VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_E3C01318498AFC31 (analyse_impact_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE aipd_modele_critere_principe_fondamental (id CHAR(36) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:uuid)\', modele_analyse_id CHAR(36) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:uuid)\', label VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, label_livrable VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, reponse VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, visible TINYINT(1) NOT NULL, texte_conformite VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, texte_non_conformite VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, non_applicable VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, justification VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_E54C824DEDA9114E (modele_analyse_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE aipd_analyse_critere_principe_fondamental ADD CONSTRAINT FK_E3C01318498AFC31 FOREIGN KEY (analyse_impact_id) REFERENCES aipd_modele (id)');
        $this->addSql('ALTER TABLE aipd_modele_critere_principe_fondamental ADD CONSTRAINT FK_E54C824DEDA9114E FOREIGN KEY (modele_analyse_id) REFERENCES aipd_modele (id)');
        $this->addSql('DROP TABLE aipd_critere_principe_fondamental');
    }
}
