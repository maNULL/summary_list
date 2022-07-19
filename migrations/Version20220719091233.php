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
            'create or replace view map_element as
                select s.summary_id as id,
                     s.accident_type as type,
                     s.accident_memo as memo,
                     a.address_text as address,
                     p.latitude,
                     p.longitude
                from summary s
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
