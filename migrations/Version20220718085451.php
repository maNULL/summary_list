<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220718085451 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Список для diff-а';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(
            '
            CREATE TABLE summary_list (
                summary_id NUMBER(10) NOT NULL,
                kusp_number NUMBER(10) DEFAULT NULL NULL,
                transfer_date DATE NOT NULL,
                accident_type VARCHAR2(255) DEFAULT NULL NULL,
                create_department VARCHAR2(255) NOT NULL,
                decision_date DATE DEFAULT NULL NULL,
                crime_type VARCHAR2(255) DEFAULT NULL NULL,
                accident_address VARCHAR2(1000) DEFAULT NULL NULL,
                complainant_full_name VARCHAR2(255) DEFAULT NULL NULL,
                criminal_code VARCHAR2(100) DEFAULT NULL NULL,
                accident_start_date DATE DEFAULT NULL NULL,
                severity VARCHAR2(255) DEFAULT NULL NULL,
                decision VARCHAR2(255) NOT NULL,
                summary_section VARCHAR2(255) DEFAULT NULL NULL,
                disclosure_unit VARCHAR2(50) DEFAULT NULL NULL,
                disclosure VARCHAR2(50) DEFAULT NULL NULL,
                registered_department VARCHAR2(255) NOT NULL,
                case_number VARCHAR2(50) DEFAULT NULL NULL,
                search_initiator VARCHAR2(10) DEFAULT NULL NULL,
                accident_memo CLOB DEFAULT NULL NULL,
                taken_measures CLOB DEFAULT NULL NULL,
                PRIMARY KEY(summary_id)
            )'
        );
        $this->addSql('COMMENT ON COLUMN summary_list.transfer_date IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN summary_list.decision_date IS \'(DC2Type:date_immutable)\'');
        $this->addSql('COMMENT ON COLUMN summary_list.accident_start_date IS \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE summary_list');
    }
}
