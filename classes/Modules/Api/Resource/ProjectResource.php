<?php

namespace Xentral\Modules\Api\Resource;

use Xentral\Components\Database\SqlQuery\SelectQuery;

class ProjectResource extends AbstractResource
{
    /**
     * @return SelectQuery
     */
    protected function selectAllQuery()
    {
        return $this->db->select()->cols(
            [
                'p.id',
                'p.name',
                'p.abkuerzung',
                'p.verantwortlicher',
                'p.beschreibung',
                'p.sonstiges',
                'p.aktiv',
                'p.farbe',
                'p.autoversand',
                'p.portocheck',
                'p.automailrechnung',
                'p.autobestellung',
                'p.speziallieferschein',
                'p.lieferscheinbriefpapier',
                'p.speziallieferscheinbeschriftung',
                'p.firma',
                'p.geloescht',
            ]
        )->from('projekt AS p')->where('p.geloescht <> 1');
    }

    /**
     * @return SelectQuery
     */
    protected function selectOneQuery()
    {
        return $this->selectAllQuery()->where('p.id = :id');
    }

    /**
     * @return SelectQuery
     */
    protected function selectIdsQuery()
    {
        return $this->selectAllQuery()->where('p.id IN (:ids)');
    }

    /** @return false */
    protected function insertQuery()
    {
        return false;
    }

    /** @return false */
    protected function updateQuery()
    {
        return false;
    }

    /** @return false */
    protected function deleteQuery()
    {
        return false;
    }

    /** @return void */
    protected function configure()
    {
    }
}
