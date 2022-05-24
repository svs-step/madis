<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220524092527 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE aipd_analyse_impact ADD label_amelioration_prevue VARCHAR(255) NOT NULL, ADD label_insatisfait VARCHAR(255) NOT NULL, ADD label_satisfaisant VARCHAR(255) NOT NULL, CHANGE conformite_traitement_id conformite_traitement_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', CHANGE avis_referent_id avis_referent_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', CHANGE avis_dpd_id avis_dpd_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', CHANGE avis_representant_id avis_representant_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', CHANGE avis_responsable_id avis_responsable_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', CHANGE date_validation date_validation DATE DEFAULT NULL');
            }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE aipd_analyse_impact DROP label_amelioration_prevue, DROP label_insatisfait, DROP label_satisfaisant, CHANGE avis_referent_id avis_referent_id CHAR(36) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:uuid)\', CHANGE avis_dpd_id avis_dpd_id CHAR(36) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:uuid)\', CHANGE avis_representant_id avis_representant_id CHAR(36) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:uuid)\', CHANGE avis_responsable_id avis_responsable_id CHAR(36) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:uuid)\', CHANGE conformite_traitement_id conformite_traitement_id CHAR(36) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:uuid)\', CHANGE date_validation date_validation DATE DEFAULT \'NULL\'');
    }
}
