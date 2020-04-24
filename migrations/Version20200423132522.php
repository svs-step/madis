<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200423132522 extends AbstractMigration
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

        $this->treatments = $this->connection->query('SELECT `id`, `observation`, `authorized_people` FROM registry_treatment WHERE `authorized_people` IS NOT NULL')->fetchAll();

        $this->addSql('ALTER TABLE registry_treatment ADD security_entitled_persons TINYINT(1) NOT NULL, ADD security_open_accounts TINYINT(1) NOT NULL, ADD security_specificities_delivered TINYINT(1) NOT NULL, ADD collecting_method VARCHAR(255) DEFAULT NULL, ADD estimated_concerned_people INT DEFAULT NULL, ADD ultimate_fate VARCHAR(255) DEFAULT NULL, CHANGE authorized_people author VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE registry_treatment ADD authorized_people VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, DROP security_entitled_persons, DROP security_open_accounts, DROP security_specificities_delivered, DROP author, DROP collecting_method, DROP estimated_concerned_people, DROP ultimate_fate');
    }

    public function postUp(Schema $schema): void
    {
        foreach ($this->treatments as $treatment) {
            $this->connection->update(
                'registry_treatment',
                [
                    'observation' => $treatment['observation'] . \chr(13) . 'Personnes habilitées : ' . $treatment['authorized_people'],
                ],
                ['id' => $treatment['id']]
            );
        }
    }
}
