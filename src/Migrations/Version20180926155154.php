<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180926155154 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE link ADD url VARCHAR(255) NOT NULL, DROP domain, DROP path, DROP full_url, DROP link');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE link ADD path VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci, ADD full_url VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, ADD link VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE url domain VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci');
    }
}
