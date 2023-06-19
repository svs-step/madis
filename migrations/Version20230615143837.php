<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230615143837 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {

        $this->addSql('ALTER TABLE user_collectivity ADD COLUMN nbr_cnil2 VARCHAR(255);');
        $this->addSql('UPDATE user_collectivity SET nbr_cnil2 = CAST(nbr_cnil AS CHAR)');
        $this->addSql('ALTER TABLE user_collectivity DROP COLUMN `nbr_cnil`, RENAME COLUMN `nbr_cnil2` to `nbr_cnil`;');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
}
