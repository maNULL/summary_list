<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220718080647 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Лицо';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(
            '
            CREATE TABLE person (
                person_id NUMBER(10) NOT NULL,
                address_id NUMBER(10) DEFAULT NULL NULL,
                summary_id NUMBER(10) NOT NULL,
                last_name VARCHAR2(32) NOT NULL,
                first_name VARCHAR2(32) NOT NULL,
                middle_name VARCHAR2(32) NOT NULL,
                birth_date DATE NOT NULL,
                PRIMARY KEY(person_id)
            )'
        );
        $this->addSql('CREATE INDEX IDX_34DCD176F5B7AF75 ON person (address_id)');
        $this->addSql('CREATE INDEX IDX_34DCD1762AC2D45C ON person (summary_id)');
        $this->addSql('COMMENT ON COLUMN person.birth_date IS \'(DC2Type:date_immutable)\'');
        $this->addSql(
            '
            ALTER TABLE person 
                ADD CONSTRAINT FK_34DCD176F5B7AF75
                    FOREIGN KEY (address_id) REFERENCES address (address_id)
                ADD CONSTRAINT FK_34DCD1762AC2D45C
                    FOREIGN KEY (summary_id) REFERENCES summary (summary_id)'
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE person');
    }
}
