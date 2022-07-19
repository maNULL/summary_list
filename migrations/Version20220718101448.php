<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220718101448 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Diff';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(
            '
            create or replace view summaries_diff as
                select SUMMARY_ID,
                       KUSP_NUMBER,
                       TRANSFER_DATE,
                       ACCIDENT_TYPE,
                       CREATE_DEPARTMENT,
                       DECISION_DATE,
                       CRIME_TYPE,
                       ACCIDENT_ADDRESS,
                       COMPLAINANT_FULL_NAME,
                       CRIMINAL_CODE,
                       ACCIDENT_START_DATE,
                       SEVERITY,
                       DECISION,
                       SUMMARY_SECTION,
                       DISCLOSURE_UNIT,
                       DISCLOSURE,
                       REGISTERED_DEPARTMENT,
                       CASE_NUMBER,
                       SEARCH_INITIATOR,
                       DBMS_LOB.SUBSTR(ACCIDENT_MEMO, 4000, 1)  as ACCIDENT_MEMO,
                       DBMS_LOB.SUBSTR(TAKEN_MEASURES, 4000, 1) as TAKEN_MEASURES
                from CURRENT_SUMMARY_LIST
                minus
                select SUMMARY_ID,
                       KUSP_NUMBER,
                       TRANSFER_DATE,
                       ACCIDENT_TYPE,
                       CREATE_DEPARTMENT,
                       DECISION_DATE,
                       CRIME_TYPE,
                       ACCIDENT_ADDRESS,
                       COMPLAINANT_FULL_NAME,
                       CRIMINAL_CODE,
                       ACCIDENT_START_DATE,
                       SEVERITY,
                       DECISION,
                       SUMMARY_SECTION,
                       DISCLOSURE_UNIT,
                       DISCLOSURE,
                       REGISTERED_DEPARTMENT,
                       CASE_NUMBER,
                       SEARCH_INITIATOR,
                       DBMS_LOB.SUBSTR(ACCIDENT_MEMO, 4000, 1)  as ACCIDENT_MEMO,
                       DBMS_LOB.SUBSTR(TAKEN_MEASURES, 4000, 1) as TAKEN_MEASURES
                from SUMMARY_LIST'
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql('drop view summaries_diff');
    }
}
