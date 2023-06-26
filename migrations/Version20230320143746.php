<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Domain\Registry\Dictionary\DelayPeriodDictionary;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Ramsey\Uuid\Uuid;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230320143746 extends AbstractMigration
{
    protected $treatments;

    public function getDescription(): string
    {
        return '';
    }

    public function preUp(Schema $schema): void
    {
        parent::preUp($schema);
        $this->treatments = $this->getData('SELECT id,delay_number,delay_period, delay_other_delay, delay_comment, ultimate_fate  FROM registry_treatment');
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE registry_treatment_shelf_life (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', treatment_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', name VARCHAR(255) DEFAULT NULL, duration TEXT DEFAULT NULL, ultimate_fate VARCHAR(255) DEFAULT NULL, INDEX IDX_FA52D4C8471C0366 (treatment_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE registry_treatment_shelf_life ADD CONSTRAINT FK_FA52D4C8471C0366 FOREIGN KEY (treatment_id) REFERENCES registry_treatment (id) ON DELETE CASCADE');

        foreach ($this->treatments as $treatment) {
            // Create shelf life for each treatment that has a delay_number
            if ($treatment['delay_number']) {
                $this->addSql('INSERT INTO registry_treatment_shelf_life(id, treatment_id, name, duration, ultimate_fate ) VALUES (?,?,?,?,?)', [Uuid::uuid4(), $treatment['id'], 'Délai de conservation', $treatment['delay_number'] . ' ' . DelayPeriodDictionary::getPeriods()[$treatment['delay_period']] . ' ' . $treatment['delay_comment'], $treatment['ultimate_fate']]);
            }
        }
        $this->addSql('ALTER TABLE registry_treatment DROP delay_number, DROP delay_period, DROP delay_other_delay, DROP delay_comment, DROP ultimate_fate, CHANGE collectivity_id collectivity_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', CHANGE creator_id creator_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', CHANGE cloned_from_id cloned_from_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', CHANGE service_id service_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', CHANGE software software VARCHAR(255) DEFAULT NULL, CHANGE legal_basis legal_basis JSON DEFAULT NULL COMMENT \'(DC2Type:json_array)\', CHANGE manager manager VARCHAR(255) DEFAULT NULL, CHANGE security_access_control_comment security_access_control_comment VARCHAR(255) DEFAULT NULL, CHANGE security_tracability_comment security_tracability_comment VARCHAR(255) DEFAULT NULL, CHANGE security_saving_comment security_saving_comment VARCHAR(255) DEFAULT NULL, CHANGE security_update_comment security_update_comment VARCHAR(255) DEFAULT NULL, CHANGE security_other_comment security_other_comment VARCHAR(255) DEFAULT NULL, CHANGE template_identifier template_identifier INT DEFAULT NULL, CHANGE data_origin data_origin VARCHAR(255) DEFAULT NULL, CHANGE author author VARCHAR(255) DEFAULT NULL, CHANGE concerned_people_particular_comment concerned_people_particular_comment VARCHAR(255) DEFAULT NULL, CHANGE concerned_people_user_comment concerned_people_user_comment VARCHAR(255) DEFAULT NULL, CHANGE concerned_people_agent_comment concerned_people_agent_comment VARCHAR(255) DEFAULT NULL, CHANGE concerned_people_elected_comment concerned_people_elected_comment VARCHAR(255) DEFAULT NULL, CHANGE concerned_people_company_comment concerned_people_company_comment VARCHAR(255) DEFAULT NULL, CHANGE concerned_people_partner_comment concerned_people_partner_comment VARCHAR(255) DEFAULT NULL, CHANGE concerned_people_other_comment concerned_people_other_comment VARCHAR(255) DEFAULT NULL, CHANGE collecting_method collecting_method JSON DEFAULT NULL COMMENT \'(DC2Type:json_array)\', CHANGE estimated_concerned_people estimated_concerned_people INT DEFAULT NULL, CHANGE coordonnees_responsable_traitement coordonnees_responsable_traitement VARCHAR(255) DEFAULT NULL, CHANGE legal_mentions legal_mentions TINYINT(1) DEFAULT \'0\', CHANGE consent_request consent_request TINYINT(1) DEFAULT \'0\', CHANGE concerned_people_usager_comment concerned_people_usager_comment VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE registry_treatment_shelf_life');
        $this->addSql('ALTER TABLE registry_treatment ADD delay_number INT DEFAULT NULL, ADD delay_period VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, ADD delay_other_delay TINYINT(1) NOT NULL, ADD delay_comment LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, ADD ultimate_fate VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE cloned_from_id cloned_from_id CHAR(36) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:uuid)\', CHANGE service_id service_id CHAR(36) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:uuid)\', CHANGE collectivity_id collectivity_id CHAR(36) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:uuid)\', CHANGE creator_id creator_id CHAR(36) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:uuid)\', CHANGE manager manager VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE legal_basis legal_basis JSON CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_bin` COMMENT \'(DC2Type:json_array)\', CHANGE data_origin data_origin VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE template_identifier template_identifier INT DEFAULT NULL, CHANGE author author VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE software software VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE coordonnees_responsable_traitement coordonnees_responsable_traitement VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE collecting_method collecting_method JSON CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_bin` COMMENT \'(DC2Type:json_array)\', CHANGE estimated_concerned_people estimated_concerned_people INT DEFAULT NULL, CHANGE legal_mentions legal_mentions TINYINT(1) DEFAULT \'0\', CHANGE consent_request consent_request TINYINT(1) DEFAULT \'0\', CHANGE security_access_control_comment security_access_control_comment VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE security_tracability_comment security_tracability_comment VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE security_saving_comment security_saving_comment VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE security_update_comment security_update_comment VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE security_other_comment security_other_comment VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE concerned_people_particular_comment concerned_people_particular_comment VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE concerned_people_user_comment concerned_people_user_comment VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE concerned_people_agent_comment concerned_people_agent_comment VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE concerned_people_elected_comment concerned_people_elected_comment VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE concerned_people_company_comment concerned_people_company_comment VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE concerned_people_partner_comment concerned_people_partner_comment VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE concerned_people_usager_comment concerned_people_usager_comment VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE concerned_people_other_comment concerned_people_other_comment VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
    }

    private function getData(string $sql): array
    {
        $stmt = $this->connection->query($sql);

        return $stmt->fetchAll();
    }
}
