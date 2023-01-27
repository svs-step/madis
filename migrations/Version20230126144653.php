<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230126144653 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE referentiel_question ADD `option` TINYINT(1) NOT NULL, ADD option_reason VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE referentiel_section ADD order_number INT NOT NULL');
        $this->addSql('ALTER TABLE referentiel_answer DROP option_not_concerned');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE referentiel_question DROP `option`, DROP option_reason');
        $this->addSql('ALTER TABLE referentiel_section DROP order_number');
    }
}
