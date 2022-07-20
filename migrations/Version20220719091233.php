<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220719091233 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'view map';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(
            'create or replace view MAP_ELEMENT as
                select s.summary_id         as id,
                       ct.title as type,
                       sl.CREATE_DEPARTMENT as create_department,
                       s.KUSP_NUMBER,
                       to_char(s.REGISTRATION_DATE, \'DD.MM.YYYY\') as registration_date,
                       to_char(sl.TRANSFER_DATE, \'DD.MM.YYYY\') as transfer_date,
                       s.accident_memo      as memo,
                       a.address_text       as address,
                       sl.SUMMARY_SECTION,
                       sl.DISCLOSURE,
                       sl.DISCLOSURE_UNIT,
                       p.latitude,
                       p.longitude,
                       ct.MARKER_COLOR,
                       case when sl.DISCLOSURE = \'Раскрыто\'
                        then \'fa-solid fa-thumbtack\' else ct.MARKER_ICON end as marker_icon
                from summary_list sl
                         left join 
                            (select s1.*, nvl(s1.crime_type_id,0) as ctid from SUMMARY s1) s on
                                sl.SUMMARY_ID = s.SUMMARY_ID
                         left join CRIME_TYPE ct on s.ctid = ct.ID
                         left join address a on s.accident_address = a.address_id
                         left join place p on p.fias_guid = a.fias_guid
                where a.house = 1
                  and p.LATITUDE is not null
                  and p.LONGITUDE is not null'
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql('drop view map_element');
    }
}
