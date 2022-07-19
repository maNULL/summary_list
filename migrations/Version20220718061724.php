<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220718061724 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Запись сводки';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(
            '
            CREATE TABLE summary (
                summary_id NUMBER(10) NOT NULL,
                kusp_id NUMBER(10) DEFAULT NULL NULL,
                department_id NUMBER(10) DEFAULT NULL NULL,
                section_id NUMBER(10) DEFAULT NULL NULL,
                crime_type_id NUMBER(10) DEFAULT NULL NULL,
                include_statistics NUMBER(1) DEFAULT NULL,
                include_statistics_date DATE DEFAULT NULL NULL,
                crime_type_extra_info VARCHAR2(1000) DEFAULT NULL NULL,
                crime_type_atts CLOB DEFAULT NULL,
                assigned_department VARCHAR2(255) DEFAULT NULL NULL,
                assigned_department_extra_info VARCHAR2(1000) DEFAULT NULL NULL,
                creator_lastname VARCHAR2(255) DEFAULT NULL NULL,
                kusp_number NUMBER(10) DEFAULT NULL NULL,
                registration_date DATE DEFAULT NULL NULL,
                accident_date DATE DEFAULT NULL NULL,
                accident_addr_extra_info VARCHAR2(1000) DEFAULT NULL NULL,
                accident_type VARCHAR2(255) DEFAULT NULL NULL,
                accident_memo CLOB DEFAULT NULL NULL,
                taken_measures CLOB DEFAULT NULL NULL,
                PRIMARY KEY(summary_id)
            )'
        );
        $this->addSql('COMMENT ON COLUMN summary.include_statistics_date IS \'(DC2Type:date_immutable)\'');
        $this->addSql('COMMENT ON COLUMN summary.registration_date IS \'(DC2Type:date_immutable)\'');
        $this->addSql('COMMENT ON COLUMN summary.accident_date IS \'(DC2Type:date_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE summary');
    }
}
