<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220926151821 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_D8F0A91E989D9B62 ON trick');
        $this->addSql('ALTER TABLE trick ADD media1 VARCHAR(255) DEFAULT NULL, ADD media2 VARCHAR(255) DEFAULT NULL, ADD media3 VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE trick DROP media1, DROP media2, DROP media3');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D8F0A91E989D9B62 ON trick (slug)');
    }
}
