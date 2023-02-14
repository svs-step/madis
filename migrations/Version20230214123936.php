<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230214123936 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE tool_treatment DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE tool_treatment ADD PRIMARY KEY (treatment_id, tool_id)');
        $this->addSql('ALTER TABLE user_collectivity ADD has_module_tools TINYINT(1) DEFAULT \'0\' NOT NULL');
}

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE tool_treatment DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE tool_treatment ADD PRIMARY KEY (tool_id, treatment_id)');
        $this->addSql('ALTER TABLE user_collectivity DROP has_module_tools');
    }
}
