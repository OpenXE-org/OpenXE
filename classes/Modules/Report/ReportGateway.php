<?php

namespace Xentral\Modules\Report;

use PHPUnit\Runner\Exception;
use Xentral\Components\Database\Database;
use Xentral\Modules\Report\Data\ReportColumn;
use Xentral\Modules\Report\Data\ReportColumnCollection;
use Xentral\Modules\Report\Data\ReportData;
use Xentral\Modules\Report\Data\ReportParameter;
use Xentral\Modules\Report\Data\ReportParameterCollection;

final class ReportGateway
{
    /** @var Database $db */
    private $db;

    /**
     * @param Database $database
     */
    public function __construct(Database $database)
    {
        $this->db = $database;
    }

    /**
     * @param string $category
     * @param string $searchTerm
     * @param int    $userId
     * @param bool   $onlyOwn
     * @param bool   $onlyFavorites
     *
     * @return array
     */
    public function getReportList($category = '', $searchTerm = '', $userId = 0, $onlyOwn = false, $onlyFavorites = false)
    {
        $result = [];
        $select = $this->db->select();
        $select->cols([
            'r.id',
            'r.name',
            'r.description',
            'r.project',
            'r.category',
            'IF(rs.file_public = 1 OR ru.file_enabled = 1 OR u.type = \'admin\', 1, 0) as allow_download',
            'IF(rs.file_csv_enabled = 1 OR u.type = \'admin\', 1, 0) as allow_csv',
            'IF(rs.file_pdf_enabled = 1 OR u.type = \'admin\', 1, 0) as allow_pdf',
            'IF(rs.menu_public = 1 OR ru.menu_enabled = 1 OR u.type = \'admin\', 1, 0) as allow_menu',
            'IF(rs.tab_public = 1 OR ru.tab_enabled = 1 OR u.type = \'admin\', 1, 0) as allow_tab',
            'IF( (rs.chart_public = 1 OR ru.chart_enabled = 1 OR u.type = \'admin\') AND rs.chart_x_column <> \'\', 1, 0) as allow_chart',
            'IF(rf.id IS NULL, 0, 1) as is_favorite',
            'r.readonly'
        ]);
        $select->from('report AS r');
        try {
            $select->leftJoin('report_share AS rs', 'r.id = rs.report_id')
                    ->leftJoin('report_favorite AS rf', '(r.id = rf.report_id AND rf.user_id = :userId)')
                    ->leftJoin('report_user AS ru', '(r.id = ru.report_id AND ru.user_id = :userId)')
                    ->leftJoin('user AS u', 'u.id = :userId');
        } catch (\Aura\SqlQuery\Exception $e) {
            return [];
        }

        if ($userId > 0) {
            $select->where(
                '(
                     (
                        ru.id IS NOT NULL 
                        OR rs.file_public = 1
                        OR rs.menu_public = 1 
                        OR rs.chart_public = 1
                        OR rs.tab_public = 1
                     )
                     AND
                     (r.project = 0 OR r.project IN (SELECT ar.projekt
                                     FROM adresse_rolle AS ar
                                     LEFT JOIN user as u ON ar.adresse = u.adresse
                                     WHERE u.id = :userId)
                     )                 
                    OR :userId IN (SELECT u.id as `c` FROM `user` AS u WHERE u.id = :userId AND u.type = \'admin\')
                )
                '
            );
        }

        if ($category !== '') {
            $select->Where('r.category LIKE :filterCategory');
        }
        if ($searchTerm !== '') {
            $select->Where('(r.name LIKE :searchTerm OR r.description LIKE :searchTerm)');
        }
        if ($userId > 0 && $onlyFavorites === true) {
            $select->Where('rf.user_id IS NOT NULL');
        }
        if ($onlyOwn === true) {
            $select->Where('r.readonly = 0');
        }

        $select->orderBy(['rf.user_id DESC', 'r.readonly ASC', 'r.name']);

        $sql = $select->getStatement();
        $values = [
            'filterCategory' => $category,
            'searchTerm' => sprintf('%%%s%%', $searchTerm),
            'userId' => $userId
        ];
        $list = $this->db->fetchAll($sql, $values);
        if (is_array($list) && count($list) > 0) {
            $result = $list;
        }

        return $result;
    }

    /**
     * @param int $id
     *
     * @return ReportData|null
     */
    public function getReportById($id)
    {
        $report = $this->getOnlyReportById($id);
        if ($report === null) {
            return null;
        }
        $columns = $this->getColumnsByReportId($id);
        $report->setColumns($columns);
        $params = $this->getParametersByReportId($id);
        $report->setParameters($params);

        return $report;
    }

    /**
     * @param string $name
     *
     * @return ReportData|null
     */
    public function findReportByName($name)
    {
        $sql = 'SELECT r.id FROM `report` AS `r` WHERE r.name=:name ORDER BY r.id LIMIT 1';
        $row = $this->db->fetchRow($sql, ['name' => $name]);
        if (!is_array($row) || count($row) < 1) {
            return null;
        }

        return $this->getReportById($row['id']);
    }

    /**
     * @param int $id
     *
     * @return ReportData|null
     */
    public function getOnlyReportById($id)
    {
        $sql = 'SELECT r.id, r.name, r.description, r.project, r.sql_query, r.remark, r.category, r.readonly,
                        r.csv_delimiter, r.csv_enclosure
                FROM `report` AS `r` 
                WHERE r.id=:idvalue';
        $values = ['idvalue' => $id];
        $row = $this->db->fetchRow($sql, $values);
        if ($row === null || count($row) === 0) {
            return null;
        }

        return ReportData::fromFormData($row);
    }

    /**
     * @param int $id
     *
     * @return ReportColumnCollection
     */
    public function getColumnsByReportId($id)
    {
        $sql = 'SELECT c.id, c.key_name, c.title, c.width, c.alignment, c.sorting, c.sum, c.sequence, c.format_type,
                        c.format_statement
                FROM `report_column` AS `c`
                WHERE c.report_id=:idvalue
                ORDER BY c.sequence';
        $values = ['idvalue' => $id];
        $rows = $this->db->fetchAll($sql, $values);

        $objects = [];
        foreach ($rows as $row) {
            $objects[] = new ReportColumn(
                $row['key_name'],
                $row['title'],
                $row['width'],
                $row['alignment'],
                $row['sum'],
                $row['id'],
                $row['sequence'],
                $row['sorting'],
                $row['format_type'],
                $row['format_statement']
            );
        }

        return new ReportColumnCollection($objects);
    }

    /**
     * @param int $id
     *
     * @return ReportParameterCollection
     */
    public function getParametersByReportId($id)
    {
        $sql = 'SELECT p.id, p.varname, p.displayname, p.default_value, p.options, p.description,
                        p.editable, p.control_type
                    FROM `report_parameter` AS `p`
                    WHERE p.report_id = :idvalue';
        $values = ['idvalue' => $id];
        $rows = $this->db->fetchAll($sql, $values);

        return ReportParameterCollection::fromFormData($rows);
    }

    /**
     * @param int $id
     *
     * @return bool
     */
    public function reportExists($id)
    {
        if ((int)$id < 1) {
            return false;
        }

        $sql = 'SELECT r.id FROM `report` AS `r` WHERE r.id = :idvalue';
        $values = ['idvalue' => (int)$id];
        $idResult = $this->db->fetchRow($sql, $values);

        return (isset($idResult['id']) && $idResult['id'] === $id);
    }

    /**
     * @param $id
     *
     * @return bool
     */
    public function isReportReadonly($id)
    {
        if ((int)$id < 1) {
            return false;
        }

        $sql = 'SELECT r.readonly FROM `report` AS `r` WHERE r.id = :idvalue';
        $values = ['idvalue' => (int)$id];
        $result = $this->db->fetchRow($sql, $values);

        return (isset($result['readonly']) && $result['readonly'] === 1);
    }

    /**
     * @param int $id
     *
     * @return bool
     */
    public function columnExists($id)
    {
        if ((int)$id < 1) {
            return false;
        }
        $sql = 'SELECT c.id FROM `report_column` AS `c` WHERE c.id = :idvalue';
        $values = ['idvalue' => (int)$id];
        $idResult = $this->db->fetchRow($sql, $values);

        return (isset($idResult['id']) && $idResult['id'] === $id);
    }

    /**
     * @param int $columnId
     *
     * @return int
     */
    public function getReportIdByColumn($columnId)
    {
        $sql = 'SELECT r.id 
                FROM `report` AS `r`
                JOIN `report_column` AS `rc` ON r.id = rc.report_id
                WHERE rc.id = :idValue';
        $values = ['idValue' => $columnId];

        return (int)$this->db->fetchValue($sql, $values);
    }

    /**
     * @param int $id
     *
     * @return bool
     */
    public function parameterExists($id)
    {
        if ((int)$id < 1) {
            return false;
        }

        $sql = 'SELECT p.id FROM `report_parameter` AS `p` WHERE p.id = :idvalue';
        $values = ['idvalue' => (int)$id];
        $idResult = $this->db->fetchRow($sql, $values);

        return (isset($idResult['id']) && $idResult['id'] === $id);
    }

    /**
     * @param int $parameterId
     *
     * @return int
     */
    public function getReportIdByParameter($parameterId)
    {
        $sql = 'SELECT r.id 
                FROM `report` AS `r`
                JOIN `report_parameter` AS `rp` ON r.id = rp.report_id
                WHERE rp.id = :idValue';
        $values = ['idValue' => $parameterId];

        return (int)$this->db->fetchValue($sql, $values);
    }

    /**
     * @param int $id
     *
     * @return ReportParameter
     */
    public function getParameterById($id)
    {
        $sql = 'SELECT p.id, p.varname, p.displayname, p.default_value, p.options, p.editable, p.description, 
                        p.control_type
                FROM `report_parameter` AS `p`
                WHERE id = :idValue';
        $values = ['idValue' => $id];
        $data = $this->db->fetchRow($sql, $values);

        return ReportParameter::fromDbState($data);
    }

    /**
     * @param int $id
     *
     * @return ReportColumn
     */
    public function getColumnById($id)
    {
        $sql = 'SELECT c.id, c.key_name, c.title, c.width, c.alignment, c.sorting, c.sum, c.sequence, c.format_type,
                        c.format_statement
                FROM `report_column` AS `c`
                WHERE c.id = :idValue';
        $values = ['idValue' => $id];
        $data = $this->db->fetchRow($sql, $values);

        return ReportColumn::fromDbState($data);
    }

    /**
     * @param int $id
     *
     * @return array
     */
    public function findTransferArrayByReportId($id)
    {
        $sql = 'SELECT id, report_id, ftp_active, ftp_passive, ftp_type, ftp_host, ftp_port, ftp_user, ftp_password,
                        ftp_interval_mode, ftp_interval_value, ftp_daytime, ftp_format, ftp_filename, ftp_last_transfer,
                        email_active, email_recipient, email_subject, email_interval_mode, email_interval_value, 
                        email_daytime, email_format, email_filename, email_last_transfer, url_format, url_begin,
                        url_end, url_address, api_active, api_account_id, api_format
                FROM `report_transfer` AS `t`
                WHERE report_id=:idValue';
        $array = $this->db->fetchRow($sql, ['idValue' => $id]);
        if ($array === null || empty($array)) {
            return [];
        }

        return $array;
    }

    /**
     * @param int    $userId
     * @param string $module
     * @param string $action
     *
     * @return array
     */
    public function findShareByModuleAction($userId, $module, $action)
    {
        $sql = "SELECT s.id, s.report_id, s.chart_public, s.chart_axislabel, s.chart_dateformat, s.chart_interval_value, 
                        s.chart_interval_mode, s.file_public, s.file_pdf_enabled, s.file_csv_enabled, 
                        s.file_xls_enabled, s.menu_public, s.menu_doctype, s.menu_label, s.menu_format, s.tab_public,
                        s.tab_module, s.tab_action, IF(s.tab_label <> '', s.tab_label, r.name) as `tab_label`,
                        s.tab_position, s.chart_type, s.chart_x_column, s.data_columns, s.chart_group_column
                FROM `report_share` AS `s`
                JOIN `report` AS `r` on s.report_id = r.id
                LEFT JOIN `report_user` AS `ru` ON r.id = ru.report_id AND ru.user_id = :userid
                WHERE (s.tab_public = 1 OR NOT ISNULL(ru.id)) AND s.tab_module = :module 
                   AND (s.tab_action = '' OR s.tab_action = :action)";
        $array = $this->db->fetchAll(
            $sql,
            [
                'userid' => (int)$userId,
                'module' => (string)$module,
                'action' => (string)$action
            ]
        );
        if ($array === null || empty($array)) {
            return [];
        }

        return $array;
    }

    /**
     * @param int $id
     *
     * @return array
     */
    public function findShareArrayByReportId($id)
    {
        $sql = 'SELECT s.id,s.report_id, s.chart_public, s.chart_axislabel, s.chart_dateformat, s.chart_interval_value, 
                        s.chart_interval_mode, s.file_public, s.file_pdf_enabled, s.file_csv_enabled, 
                        s.file_xls_enabled, s.menu_public, s.menu_doctype, s.menu_label, s.menu_format, s.tab_public,
                        s.tab_module, s.tab_action, s.tab_label, s.tab_position, s.chart_type, 
                        s.chart_x_column, s.data_columns, s.chart_group_column
                FROM `report_share` AS `s`
                WHERE s.report_id=:idValue';
        $array = $this->db->fetchRow($sql, ['idValue' => $id]);
        if ($array === null || empty($array)) {
            return [];
        }

        return $array;
    }

    /**
     * @param int $id
     *
     * @return array
     */
    public function findSharedUserById($id)
    {
        $sql = 'SELECT ru.id, ru.report_id, ru.user_id, ru.name, ru.chart_enabled, ru.file_enabled, 
                        ru.menu_enabled, ru.tab_enabled
                FROM `report_user` AS `ru`
                WHERE ru.id=:idValue';
        $array = $this->db->fetchRow($sql, ['idValue' => $id]);
        if ($array === null || empty($array)) {
            return [];
        }

        return $array;
    }

    /**
     * @param int $userId
     * @param int $reportId
     *
     * @return bool
     */
    public function isSharedUserOfReport($userId, $reportId)
    {
        $sql = 'SELECT ru.id FROM `report_user` AS `ru` WHERE ru.user_id = :userId AND ru.report_id = :reportId';
        $values = ['userId' => $userId, 'reportId' => $reportId];
        $id = $this->db->fetchValue($sql, $values);

        return !empty($id);
    }

    /**
     * @param string  $doctype
     * @param int     $userId
     *
     * @return array
     */
    public function getDocumentAddActionMenuData($doctype, $userId = 0)
    {
        $sql = 'SELECT r.id, r.name, s.menu_doctype, s.menu_format, s.menu_label
                FROM `report` as `r`
                JOIN `report_share` as `s` ON r.id = s.report_id
                LEFT JOIN `report_user` as `u` ON r.id = u.report_id
                WHERE (s.menu_doctype = :docType AND (s.menu_public = 1 OR (u.user_id = :userId AND u.menu_enabled = 1))
                AND
                    (r.project = 0 OR r.project IN (SELECT ar.projekt
                                        FROM `adresse_rolle` AS `ar`
                                        LEFT JOIN `user` as `u` ON ar.adresse = u.adresse
                                        WHERE u.id = :userId)
                OR :userId IN (SELECT u.id as `c` FROM `user` AS `u` 
                WHERE u.id = :userId AND u.type = \'admin\'))
                    
                    
                    )';
        $data = $this->db->fetchAll($sql, ['docType' => strtolower($doctype), 'userId' => $userId]);
        if (empty($data)) {
            return [];
        }

        return $data;
    }

    /**
     * @param int $reportId
     * @param int $userId
     *
     * @return bool
     */
    public function isFavoriteReportOfUser($reportId, $userId)
    {
        if ($reportId < 1 || $userId < 0){
            return false;
        }
        $sql = 'SELECT rf.id 
                FROM `report_favorite` AS `rf` 
                WHERE rf.report_id = :reportID AND rf.user_id = :userId
                LIMIT 1';
        $id = $this->db->fetchValue($sql, ['reportID' => $reportId, 'userId' => $userId]);

        return !empty($id);
    }

    /**
     * @param $reportId
     * @param $userId
     *
     * @return bool
     */
    public function userCanDownloadCsv($reportId, $userId)
    {
        return $this->userCanDownload($reportId, $userId, 'csv');
    }

    /**
     * @param $reportId
     * @param $userId
     *
     * @return bool
     */
    public function userCanDownloadPdf($reportId, $userId)
    {
        return $this->userCanDownload($reportId, $userId, 'pdf');
    }

    /**
     * @todo: implement when xls exporter is ready
     *
     * @param $reportId
     * @param $userId
     *
     * @return bool
     */
    public function userCanDownloadXls($reportId, $userId)
    {
        //return $this->userCanDownload($reportId, $userId, 'xls');

        return false;
    }

    /**
     * @param $reportId
     * @param $userId
     *
     * @param $filetype 'csv', 'pdf' or 'xls'
     *
     * @return bool
     */
    private function userCanDownload($reportId, $userId, $filetype)
    {
        try {
            $sql = sprintf(
                'SELECT r.id
                        FROM `report` AS `r`
                        LEFT JOIN `report_share` AS `s` ON r.id = s.report_id
                        LEFT JOIN `report_user` AS `u` ON r.id = u.report_id
                        WHERE r.id = :reportId AND (
                            :userId IN (SELECT `u`.`id` AS `c` FROM `user` AS `u` WHERE `u`.`id` = :userId AND `u`.`type` = \'admin\')
                            OR
                            (s.file_%s_enabled = 1 AND (s.file_public = 1 OR (u.user_id = :userId AND u.file_enabled = 1)))
                            )',
                $filetype
            );
            $data = $this->db->fetchAll($sql, ['reportId' => $reportId, 'userId' => $userId]);

            return !empty($data);
        } catch (Exception $e) {
            return false;
        }
    }
}
