<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200409132217 extends AbstractMigration
{
    private $treatments;

    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->treatments = $this->connection->query('SELECT `id`, `concerned_people` FROM registry_treatment')->fetchAll();

        $this->addSql('ALTER TABLE registry_treatment ADD concerned_people_particular_check TINYINT(1) NOT NULL, ADD concerned_people_particular_comment VARCHAR(255) DEFAULT NULL, ADD concerned_people_user_check TINYINT(1) NOT NULL, ADD concerned_people_user_comment VARCHAR(255) DEFAULT NULL, ADD concerned_people_agent_check TINYINT(1) NOT NULL, ADD concerned_people_agent_comment VARCHAR(255) DEFAULT NULL, ADD concerned_people_elected_check TINYINT(1) NOT NULL, ADD concerned_people_elected_comment VARCHAR(255) DEFAULT NULL, ADD concerned_people_company_check TINYINT(1) NOT NULL, ADD concerned_people_company_comment VARCHAR(255) DEFAULT NULL, ADD concerned_people_partner_check TINYINT(1) NOT NULL, ADD concerned_people_partner_comment VARCHAR(255) DEFAULT NULL, ADD concerned_people_other_check TINYINT(1) NOT NULL, ADD concerned_people_other_comment VARCHAR(255) DEFAULT NULL, DROP concerned_people');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE registry_treatment ADD concerned_people JSON NOT NULL COMMENT \'(DC2Type:json_array)\', DROP concerned_people_particular_check, DROP concerned_people_particular_comment, DROP concerned_people_internet_user_check, DROP concerned_people_internet_user_comment, DROP concerned_people_agent_check, DROP concerned_people_agent_comment, DROP concerned_people_elected_check, DROP concerned_people_elected_comment, DROP concerned_people_company_check, DROP concerned_people_company_comment, DROP concerned_people_partner_check, DROP concerned_people_partner_comment, DROP concerned_people_other_check, DROP concerned_people_other_comment');
    }

    public function postUp(Schema $schema): void
    {
        foreach ($this->treatments as $treatment) {
            $concernedPeople = json_decode($treatment['concerned_people'], true);
            $this->connection->update(
                'registry_treatment',
                [
                    'concerned_people_particular_check' => \in_array('particular', $concernedPeople) ? 1 : 0,
                    'concerned_people_user_check'       => \in_array('user', $concernedPeople) ? 1 : 0,
                    'concerned_people_agent_check'      => \in_array('agent', $concernedPeople) ? 1 : 0,
                    'concerned_people_elected_check'    => \in_array('elected', $concernedPeople) ? 1 : 0,
                    'concerned_people_company_check'    => \in_array('company', $concernedPeople) ? 1 : 0,
                    'concerned_people_partner_check'    => \in_array('partner', $concernedPeople) ? 1 : 0,
                ],
                ['id' => $treatment['id']]
            );
        }
    }
}
