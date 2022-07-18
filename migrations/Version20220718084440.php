<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220718084440 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Адрес в происшествии';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE SUMMARY ADD (accident_address NUMBER(10) DEFAULT NULL NULL)');
        $this->addSql(
            '
            ALTER TABLE SUMMARY
                ADD CONSTRAINT FK_CE2866632599981F
                    FOREIGN KEY (accident_address) REFERENCES address (address_id)'
        );
        $this->addSql('CREATE INDEX IDX_CE2866632599981F ON SUMMARY (accident_address)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE summary DROP CONSTRAINT FK_CE2866632599981F');
        $this->addSql('DROP INDEX IDX_CE2866632599981F');
        $this->addSql('ALTER TABLE summary DROP (accident_address)');
    }
}
