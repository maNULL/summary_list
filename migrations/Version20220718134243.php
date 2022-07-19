<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220718134243 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'cascade delete';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE PERSON DROP CONSTRAINT FK_34DCD1762AC2D45C');
        $this->addSql(
            '
            ALTER TABLE PERSON 
                ADD CONSTRAINT FK_34DCD1762AC2D45C
                    FOREIGN KEY (summary_id)
                        REFERENCES summary (summary_id) ON DELETE CASCADE'
        );
        $this->addSql(
            '
            ALTER TABLE SUMMARY
                ADD CONSTRAINT FK_CE2866632AC2D45C
                    FOREIGN KEY (summary_id)
                        REFERENCES summary_list (summary_id) ON DELETE CASCADE'
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE summary DROP CONSTRAINT FK_CE2866632AC2D45C');
        $this->addSql('ALTER TABLE person DROP CONSTRAINT FK_34DCD1762AC2D45C');
        $this->addSql(
            '
            ALTER TABLE person
                DROP CONSTRAINT FK_34DCD1762AC2D45C
                ADD CONSTRAINT FK_34DCD1762AC2D45C
                    FOREIGN KEY (SUMMARY_ID) REFERENCES SUMMARY (SUMMARY_ID)'
        );
    }
}
