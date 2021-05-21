<?php

namespace Xentral\Modules\Report;

use DateTime;
use Exception;
use Xentral\Components\Database\Database;
use Xentral\Components\Util\StringUtil;
use Xentral\Modules\Report\Data\ReportColumn;
use Xentral\Modules\Report\Data\ReportColumnCollection;
use Xentral\Modules\Report\Data\ReportData;
use Xentral\Modules\Report\Data\ReportParameter;
use Xentral\Modules\Report\Data\ReportParameterCollection;
use Xentral\Modules\Report\Exception\ColumnFormatException;
use Xentral\Modules\Report\Exception\DatabaseTransactionException;
use Xentral\Modules\Report\Exception\InvalidArgumentException;
use Xentral\Modules\Report\Exception\ParameterNameException;
use Xentral\Modules\Report\Exception\ReportSqlQueryException;
use Xentral\Modules\Report\Exception\UnresolvedParameterException;

final class ReportService
{
    /** @var Database $db */
    private $db;

    /** @var ReportGateway $gateway */
    private $gateway;

    /** @var ReportResolveParameterService $resolver */
    private $resolver;

    /**
     * @param Database                      $db
     * @param ReportGateway                 $gateway
     * @param ReportResolveParameterService $resolver
     */
    public function __construct(Database $db, ReportGateway $gateway, ReportResolveParameterService $resolver)
    {
        $this->db = $db;
        $this->gateway = $gateway;
        $this->resolver = $resolver;
    }

    /**
     * @param $sqlStatement
     *
     * @return bool
     */
    public function isSqlStatementAllowed($sqlStatement)
    {
        $filterKeyWords = [
            'INTO',
            'INSERT',
            'UPDATE',
            'DELETE',
            'ALTER',
            'SHOW',
            'USE',
            'TRUNCATE',
            'LOAD',
            'CREATE',
            'DROP',
            'RENAME',
        ];

        foreach ($filterKeyWords as $keyWord) {
            $allOccurances = [];
            $escapedOccurances = [];
            $allPattern = sprintf('/%s/i', $keyWord);
            $escapedPattern = sprintf('/\W[`\'][^`\']*%s[^`\']*[`\']\W?/i', $keyWord);
            if (preg_match_all($allPattern, $sqlStatement, $allOccurances)) {
                preg_match_all($escapedPattern, $sqlStatement, $escapedOccurances);
                if (count($allOccurances[0]) !== count($escapedOccurances[0])) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * @param ReportData $report
     *
     * @return int
     */
    public function saveReport(ReportData $report)
    {
        $id = $report->getId();
        if ($id !== null && $id > 0 && $this->gateway->reportExists($id)) {
            $newId = $this->updateReport($report);
        } else {
            $newId = $this->insertReport($report);
        }

        return $newId;
    }

    /**
     * @param int $id
     *
     * @throws InvalidArgumentException
     * @throws DatabaseTransactionException
     *
     * @return int
     */
    public function copyReport($id = 0)
    {
        $report = $this->gateway->getReportById($id);
        if ($report === null) {
            throw new InvalidArgumentException(sprintf('Report with id %s not found.', $id));
        }

        $newParameters = null;
        $parameters = $report->getParameters();
        if ($parameters !== null) {
            $newParameters = [];
            foreach ($parameters as $param) {
                $data = $param->toArray();
                $data['id'] = 0;
                $newParameters[] = ReportParameter::fromDbState($data);
            }
        }

        $newColumns = null;
        $columns = $report->getColumns();
        if ($columns !== null) {
            $newColumns = [];
            foreach ($columns as $column) {
                $data = $column->toArray();
                $data['id'] = 0;
                $temp = ReportColumn::fromDbState($data);
                $newColumns[] = $temp;
            }
        }

        $newName = $this->generateIncrementedReportName($report->getName());
        $newReport = new ReportData(
            $newName,
            $report->getDescription(),
            $report->getProjectId(),
            $report->getSqlQuery(),
            new ReportColumnCollection($newColumns),
            new ReportParameterCollection($newParameters),
            0,
            $report->getRemark(),
            $report->getCategory(),
            false
        );

        return $this->insertReport($newReport);
    }

    /**
     * @param ReportColumnCollection $columns
     * @param int                    $reportId
     */
    public function saveColumnCollection(ReportColumnCollection $columns, $reportId)
    {
        foreach ($columns as $column) {
            $this->saveColumn($column, $reportId);
        }
    }

    /**
     * @param ReportColumn       $column
     * @param                    $reportId
     *
     * @return int
     */
    public function saveColumn(ReportColumn $column, $reportId)
    {
        $id = $column->getId();
        if ($id !== null && $id > 0 && $this->gateway->columnExists($id)) {
            $newId = $this->updateColumn($column, $reportId);
        } else {
            $newId = $this->insertColumn($column, $reportId);
        }

        return $newId;
    }

    /**
     * @param ReportParameterCollection $parameters
     * @param int                       $reportId
     */
    public function saveParameterCollection(ReportParameterCollection $parameters, $reportId)
    {
        foreach ($parameters as $param) {
            $this->saveParameter($param, $reportId);
        }
    }

    /**
     * @param ReportParameter $parameter
     * @param int             $reportId
     *
     * @return int
     */
    public function saveParameter(ReportParameter $parameter, $reportId)
    {
        $id = $parameter->getId();
        if ($id !== null && $id > 0 && $this->gateway->parameterExists($id)) {
            $newId = $this->updateParameter($parameter, $reportId);
        } else {
            $newId = $this->insertParameter($parameter, $reportId);
        }

        return $newId;
    }

    /**
     * @param array $transferArray
     *
     * @return int
     */
    public function saveTransferArray($transferArray)
    {
        $id = (int)$transferArray['id'];
        $reportId = (int)$transferArray['report_id'];
        if (!$this->gateway->reportExists($reportId)) {
            return 0;
        }

        if ($id !== null && $id > 0) {
            $newId = $this->updateTransferArray($transferArray);
        } else {
            $newId = $this->insertTransferArray($transferArray);
        }

        return $newId;
    }

    /**
     * @param array $shareArray
     *
     * @return int
     */
    public function saveShareArray($shareArray)
    {
        $id = (int)$shareArray['id'];
        $reportId = (int)$shareArray['report_id'];
        if (!$this->gateway->reportExists($reportId)) {
            return 0;
        }

        if ($id !== null && $id > 0) {
            $newId = $this->updateShareArray($shareArray);
        } else {
            $newId = $this->insertShareArray($shareArray);
        }

        return $newId;
    }

    /**
     * @param array $reportUserArray
     *
     * @return int
     */
    public function saveReportUserArray($reportUserArray)
    {
        $id = (int)$reportUserArray['id'];
        $reportId = (int)$reportUserArray['report_id'];
        if (!$this->gateway->reportExists($reportId)) {
            return 0;
        }
        if ($id !== null && $id > 0) {
            $newId = $this->updateReportUserArray($reportUserArray);
        } else {
            $newId = $this->insertReportUserArray($reportUserArray);
        }

        return $newId;
    }

    /**
     * @param int $id
     *
     * @throws InvalidArgumentException
     *
     * @return void
     */
    public function deleteColumnById($id)
    {
        if ($id < 1) {
            throw new InvalidArgumentException('Cannot delete Column without valid ID.');
        }
        $sql = 'DELETE FROM `report_column` WHERE id=:idvalue';
        $values = ['idvalue' => $id];

        $this->db->perform($sql, $values);
    }

    /**
     * @param $reportId
     *
     * @return int amount of deleted columns
     */
    public function deleteAllColumnsByReportId($reportId)
    {
        $sql = 'DELETE FROM `report_column` WHERE report_id = :reportId';

        return $this->db->fetchAffected($sql, ['reportId' => $reportId]);
    }

    /**
     * @param int $id
     *
     * @throws DatabaseTransactionException
     *
     * @return void
     */
    public function deleteReportById($id)
    {
        if ($id < 1) {
            throw new InvalidArgumentException('Cannot delete Report without valid ID.');
        }
        $sqlDeleteReport = 'DELETE FROM `report` WHERE id=:idValue';
        $sqlDeleteParam = 'DELETE FROM `report_parameter` WHERE report_id=:idValue';
        $sqlDeleteColumn = 'DELETE FROM `report_column` WHERE report_id=:idValue';
        $sqlDeleteShare = 'DELETE FROM `report_share` WHERE report_id=:idValue';
        $sqlDeleteTransfer = 'DELETE FROM `report_transfer` WHERE report_id=:idValue';
        $sqlDeleteSharedUser = 'DELETE FROM `report_user` WHERE report_id=:idValue';
        $sqlDeleteFavorites = 'DELETE FROM `report_favorite` WHERE report_id=:idValue';
        $values = ['idValue' => $id];
        $this->db->beginTransaction();
        try {
            $this->db->perform($sqlDeleteParam, $values);
            $this->db->perform($sqlDeleteColumn, $values);
            $this->db->perform($sqlDeleteReport, $values);
            $this->db->perform($sqlDeleteShare, $values);
            $this->db->perform($sqlDeleteTransfer, $values);
            $this->db->perform($sqlDeleteSharedUser, $values);
            $this->db->perform($sqlDeleteFavorites, $values);
        } catch (Exception $e) {
            $this->db->rollBack();
            throw new DatabaseTransactionException('Failed to delete report', $e->getCode(), $e);
        }
        $this->db->commit();
    }

    /**
     * @param int $id
     *
     * @return bool true = success
     */
    public function tryDeleteUserShare($id)
    {
        if ($id < 1) {
            return false;
        }
        $sql = 'DELETE FROM `report_user` WHERE id = :idValue';
        $countAffected = $this->db->fetchAffected($sql, ['idValue' => $id]);

        return $countAffected > 0;
    }

    /**
     * @param int      $reportId
     * @param DateTime $dateTime
     *
     * @return bool true = success
     */
    public function setLastTransferFtp($reportId, DateTime $dateTime)
    {
        $sql = 'UPDATE `report_transfer` SET ftp_last_transfer = :dateValue WHERE report_id = :idValue';
        $values = ['dateValue' => $dateTime->format('Y-m-d H:i:s'), 'idValue' => (int)$reportId];
        $affectedRows = $this->db->fetchAffected($sql, $values);

        return !($affectedRows < 1);
    }

    /**
     * @param int      $reportId
     * @param DateTime $dateTime
     *
     * @return bool true = success
     */
    public function setLastTransferEmail($reportId, DateTime $dateTime)
    {
        $sql = 'UPDATE `report_transfer` SET email_last_transfer = :dateValue WHERE report_id = :idValue';
        $values = ['dateValue' => $dateTime->format('Y-m-d H:i:s'), 'idValue' => (int)$reportId];
        $affectedRows = $this->db->fetchAffected($sql, $values);

        return !($affectedRows < 1);
    }

    /**
     * @param $id
     */
    public function deleteParamById($id)
    {
        if ($id < 1) {
            throw new InvalidArgumentException('Cannot delete Parameter without valid ID.');
        }
        $sql = 'DELETE FROM report_parameter WHERE id=:idvalue';
        $values = ['idvalue' => $id];

        $this->db->perform($sql, $values);
    }

    /**
     * @param ReportData $report
     *
     * @param array      $parameterValues
     *
     * @return string sql statement with inserted variable values
     */
    public function resolveParameters(ReportData $report, $parameterValues = [])
    {
        return $this->resolver->resolveParameters($report, $parameterValues);
    }

    /**
     * @param string $statement
     * @param array  $parameters
     *
     * @return array
     */
    public function testSqlStatement($statement, $parameters = [])
    {
        //1. error: statement empty
        if ($statement === '') {
            $testResult['messagetype'] = 'info';
            $testResult['message'] = 'Cannot execute empty statement';

            return $testResult;
        }

        //2. error: invalid parameter names
        $testReport = new ReportData('', '', '', $statement);
        $params = [];
        foreach ($parameters as $name => $value) {
            try {
                $params[] = new ReportParameter($name, $value);
            } catch (ParameterNameException $e) {
                $testResult['messagetype'] = 'error';
                $testResult['message'] = sprintf(
                    'Invalid Parameter name "%s".
                    Parameter names can only contain letters, numbers and "_".
                    Parameter names must start with a letter.',
                    $name
                );

                return $testResult;
            }
        }
        $testReport->setParameters(new ReportParameterCollection($params));

        //3. error: wrong variable names in query
        $errorVarnames = $this->resolver->findInvalidVariableNames($statement);
        if (count($errorVarnames) > 0) {
            $testResult['messagetype'] = 'error';
            $testResult['message'] = sprintf(
                'Invalid Variable Names: %s. Variable names can only contain letters A-Z, numbers and "_".',
                implode(', ', $errorVarnames)
            );

            return $testResult;
        }

        //4. error: unresolved Variables
        try {
            $compiled = $this->resolveParameters($testReport);
        } catch (UnresolvedParameterException $e) {
            $testResult['messagetype'] = 'error';
            $testResult['message'] = $e->getMessage();

            return $testResult;
        }

        //5. error: Syntax error
        if (!$this->isSqlStatementAllowed($compiled)) {
            $testResult['messagetype'] = 'error';
            $testResult['message'] = 'Reports can only use SELECT statements!';

            return $testResult;
        }

        //6. error: Query Semantic error
        if(!preg_match('/LIMIT\s*\d/', $compiled)){
            $compiled .= " LIMIT 101";
        }
        try {
            $rows = $this->db->fetchAll($compiled);
            $columnNames = [];
            if (is_array($rows) && count($rows) > 0) {
                $columnNames = array_keys($rows[0]);
            }
        } catch (Exception $e) {
            $testResult['messagetype'] = 'error';
            $testResult['message'] = sprintf("QUERY FAILED:\n%s", $e->getMessage());

            return $testResult;
        }

        //everything fine
        $message = "Query successful: More than 100 datasets found";
        if(count($rows) < 101){
            $message = sprintf('Query successful: %s datasets found', count($rows));
        }
        $testResult = [
            'messagetype' => 'success',
            'message'     => $message,
            'columnnames' => $columnNames,
        ];

        return $testResult;
    }

    /**
     * Finds possible copied reports of the name and
     * generates a report name with increment if needed.
     *
     * @example  generateIncrementedReportName('Existing Report') -> 'Existing Report (1)'
     * @example  generateIncrementedReportName('Existing Report (1)') -> 'Existing Report (2)'
     * @example  generateIncrementedReportName('New Report') -> 'New Report'
     *
     * @param string $reportName
     *
     * @param int    $ignoreId
     *
     * @return string
     */
    public function generateIncrementedReportName($reportName, $ignoreId = 0)
    {
        $newName = $this->getBaseName($reportName);
        $copies = $this->findReportCopyNames($newName, $ignoreId);
        if (count($copies) > 0) {
            $maxIncrement = 0;
            foreach ($copies as $name) {
                preg_match('/^.+\s*\((\d+)\)$/', $name, $matches);
                if (isset($matches[1]) && (int)$matches[1] > $maxIncrement) {
                    $maxIncrement = (int)$matches[1];
                }
            }
            $newName = sprintf('%s (%s)', $newName, $maxIncrement + 1);
        }

        return $newName;
    }

    /*
     *
     */
    public function autoCreateColumns(ReportData $report)
    {
        try {
            $compiledStatement = $this->resolveParameters($report);
            $sampleRow = $this->db->fetchRow($compiledStatement);
        } catch (Exception $e) {
            throw new ReportSqlQueryException('Query delivered no result.');
        }
        $resultKeys = array_keys($sampleRow);
        $existingKeys = [];
        $columns = $report->getColumns();
        $columnsArray = [];
        $totalWidth = 0;
        $maxSequence = 0;
        if ($columns !== null) {
            foreach ($columns as $column) {
                $columnsArray[] = $column;
                $totalWidth += (int)$column->getWidth();
                $existingKeys[] = $column->getKey();
                if ($column->getSequence() > $maxSequence) {
                    $maxSequence = $column->getSequence();
                }
            }
        }

        $addKeys = [];
        foreach ($resultKeys as $key) {
            if (!in_array($key, $existingKeys, true)) {
                $addKeys[] = $key;
            }
        }

        if (count($addKeys) === 0) {
            return $report;
        }
        $width = floor((190 - $totalWidth) / count($addKeys));
        $sequence = $maxSequence + 1;
        foreach ($addKeys as $addKey) {
            $newCol = new ReportColumn(
                $addKey,
                StringUtil::toTitleCase($addKey),
                $width,
                ReportColumn::ALIGN_LEFT,
                false,
                0,
                $sequence
            );
            $columnsArray[] = $newCol;
            $sequence++;
        }
        $columnCollection = new ReportColumnCollection($columnsArray);
        $report->setColumns($columnCollection);

        return $report;
    }

    /**
     * @param int $reportId
     * @param int $userId
     *
     * @return bool success
     */
    public function addReportFavorite($reportId, $userId)
    {
        if ($reportId < 1 || $userId < 1) {
            return false;
        }
        if ($this->gateway->isFavoriteReportOfUser($reportId, $userId)) {
            return true;
        }
        $sql = 'INSERT INTO `report_favorite` (report_id, user_id) VALUES (:reportId, :userId)';
        $affected = $this->db->fetchAffected($sql, ['reportId' => $reportId, 'userId' => $userId]);

        return $affected > 0;
    }

    /**
     * @param int $reportId
     * @param int $userId
     *
     * @return bool success
     */
    public function removeReportFavorite($reportId, $userId)
    {
        if ($reportId < 1 || $userId < 1) {
            return false;
        }
        if (!$this->gateway->isFavoriteReportOfUser($reportId, $userId)) {
            return true;
        }
        $sql = 'DELETE FROM `report_favorite` WHERE report_id = :reportId AND user_id = :userId';
        $affected = $this->db->fetchAffected($sql, ['reportId' => $reportId, 'userId' => $userId]);

        return $affected > 0;
    }

    /**
     * @param string $format
     *
     * @throws ColumnFormatException
     *
     * @return void
     */
    public  function validateCustomColumnFormat(string $format): void
    {
        if (!$this->isSqlStatementAllowed($format)) {
            throw new ColumnFormatException('invalid column format statement');
        }
        if (preg_match('/{VALUE}/i', $format) !== 1) {
            throw new ColumnFormatException('Format statement must contain "{VALUE}" variable.');
        }
    }

    /**
     * @param ReportData $report
     *
     * @throws DatabaseTransactionException
     *
     * @return int
     */
    private function insertReport(ReportData $report)
    {
        $sql = 'INSERT INTO report (name, description, project, sql_query, remark, category, readonly,
                                    csv_delimiter, csv_enclosure)
                VALUES (:name, :description, :project, :sql_query, :remark, :category, :readonly,
                                    :csv_delimiter, :csv_enclosure)';
        $values = [
            'name'          => $report->getName(),
            'description'   => $report->getDescription(),
            'project'       => $report->getProjectId(),
            'sql_query'     => $report->getSqlQuery(),
            'remark'        => $report->getRemark(),
            'category'      => $report->getCategory(),
            'readonly'      => 0,
            'csv_delimiter' => $report->getCsvDelimiter(),
            'csv_enclosure' => $report->getCsvEnclosure(),
        ];
        if ($report->isReadonly()) {
            $values['readonly'] = 1;
        }

        $this->db->beginTransaction();
        try {
            $this->db->perform($sql, $values);
            $insertId = $this->db->lastInsertId();
            $columns = $report->getColumns();
            if ($columns !== null && $columns !== []) {
                $this->saveColumnCollection($columns, $insertId);
            }
            $params = $report->getParameters();
            if ($params !== null && $params !== []) {
                $this->saveParameterCollection($params, $insertId);
            }
        } catch (Exception $e) {
            $this->db->rollBack();
            throw new DatabaseTransactionException($e->getMessage(), $e->getCode(), $e);
        }
        $this->db->commit();

        return $insertId;
    }

    /**
     * @param ReportData $report
     *
     * @return int
     */
    private function updateReport(ReportData $report)
    {
        $id = $report->getId();
        $sql = 'UPDATE report 
                SET name=:name, description=:description, project=:project, sql_query=:sql_query, remark=:remark, 
                    category=:category, csv_delimiter=:csv_delimiter, csv_enclosure=:csv_enclosure, readonly=:readonly
                WHERE id=:idvalue';
        $values = [
            'name'          => $report->getName(),
            'description'   => $report->getDescription(),
            'project'       => $report->getProjectId(),
            'sql_query'     => $report->getSqlQuery(),
            'idvalue'       => $id,
            'remark'        => $report->getRemark(),
            'category'      => $report->getCategory(),
            'csv_delimiter' => $report->getCsvDelimiter(),
            'csv_enclosure' => $report->getCsvEnclosure(),
            'readonly'      => (int)$report->isReadonly(),
        ];

        $this->db->beginTransaction();
        try {
            $this->db->perform($sql, $values);
            $columns = $report->getColumns();
            if ($columns !== null && $columns !== []) {
                $this->saveColumnCollection($columns, $id);
            }
            $params = $report->getParameters();
            if ($params !== null && $params !== []) {
                $this->saveParameterCollection($params, $id);
            }
        } catch (Exception $e) {
            $this->db->rollBack();
            throw new DatabaseTransactionException($e->getMessage(), $e->getCode(), $e);
        }
        $this->db->commit();

        return $id;
    }

    /**
     * @param ReportColumn $column
     * @param int          $reportId
     *
     * @return int
     */
    private function insertColumn(ReportColumn $column, $reportId)
    {
        $sql = 'INSERT INTO report_column 
                (report_id, key_name, title, width, alignment, sorting, sum, sequence, format_type, format_statement)
                VALUES (:reportId, :keyName, :title, :width, :alignment, :sorting, :sum, :sequence,
                        :format_type, :format_statement)';
        $sum = 0;
        if ($column->isSumColumn()) {
            $sum = 1;
        }
        $values = [
            'reportId'  => $reportId,
            'keyName'   => $column->getKey(),
            'title'     => $column->getTitle(),
            'width'     => $column->getWidth(),
            'alignment' => $column->getAlignment(),
            'sorting'   => $column->getSorting(),
            'sum'       => $sum,
            'sequence'  => $column->getSequence(),
            'format_type' => $column->getFormatType(),
            'format_statement' => $column->getFormatStatement(),
        ];
        $this->db->perform($sql, $values);

        return $this->db->lastInsertId();
    }

    /**
     * @param ReportColumn $column
     * @param int          $reportId
     *
     * @return int
     */
    private function updateColumn(ReportColumn $column, $reportId)
    {
        $id = $column->getId();
        $sql = 'UPDATE report_column 
                SET report_id = :reportId, key_name = :keyName, title = :title, width = :width, alignment = :alignment,
                    sorting = :sorting, sum = :sum, sequence = :sequence, format_type = :format_type,
                    format_statement = :format_statement
                WHERE id = :idvalue';
        $sum = 0;
        if ($column->isSumColumn()) {
            $sum = 1;
        }
        $values = [
            'reportId'  => $reportId,
            'keyName'   => $column->getKey(),
            'title'     => $column->getTitle(),
            'width'     => $column->getWidth(),
            'alignment' => $column->getAlignment(),
            'sorting'   => $column->getSorting(),
            'sum'       => $sum,
            'sequence'  => $column->getSequence(),
            'format_type' => $column->getFormatType(),
            'format_statement' => $column->getFormatStatement(),
            'idvalue' => $id,
        ];
        $this->db->perform($sql, $values);

        return $id;
    }

    /**
     * @param ReportParameter $parameter
     * @param int             $reportId
     *
     * @return int
     */
    private function insertParameter(ReportParameter $parameter, $reportId)
    {
        $sql = 'INSERT INTO report_parameter
                (report_id, varname, displayname, default_value, options, description, editable, control_type)
                VALUES 
                (:reportId, :varName, :displayName, :defaultValue, :options, :description, :editable, :controlType)';
        $editable = 0;
        if ($parameter->isEditable()) {
            $editable = 1;
        }
        $values = [
            'reportId'     => $reportId,
            'varName'      => $parameter->getVarname(),
            'displayName'  => $parameter->getDisplayname(),
            'defaultValue' => $parameter->getDefaultValue(),
            'options'      => $parameter->getOptionsAsString(),
            'description'  => $parameter->getDescription(),
            'editable'     => $editable,
            'controlType'  => $parameter->getControlType(),
        ];
        $this->db->perform($sql, $values);

        return $this->db->lastInsertId();
    }

    /**
     * @param ReportParameter $parameter
     * @param int             $reportId
     *
     * @return int
     */
    private function updateParameter(ReportParameter $parameter, $reportId)
    {
        $id = $parameter->getId();
        $sql = 'UPDATE report_parameter
                SET report_id=:reportId, varname=:varName, displayname=:displayName, default_value=:defaultValue,
                    options=:options, description=:description, editable=:editable, control_type=:controlType
                WHERE id=:idvalue';
        $editable = 0;
        if ($parameter->isEditable()) {
            $editable = 1;
        }
        $values = [
            'reportId'     => $reportId,
            'varName'      => $parameter->getVarname(),
            'displayName'  => $parameter->getDisplayname(),
            'defaultValue' => $parameter->getDefaultValue(),
            'options'      => $parameter->getOptionsAsString(),
            'description'  => $parameter->getDescription(),
            'editable'     => $editable,
            'idvalue'      => $id,
            'controlType'  => $parameter->getControlType(),
        ];
        $this->db->perform($sql, $values);

        return $id;
    }

    /**
     * @param $transferArray
     *
     * @return array
     */
    private function sanitizeTransferDateTimes($transferArray)
    {
        $dateTimeKeys = [
            'ftp_daytime',
            'ftp_last_transfer',
            'email_daytime',
            'email_last_transfer',
            'url_begin',
            'url_end',
        ];
        foreach ($dateTimeKeys as $key) {
            if (isset($transferArray[$key]) && empty($transferArray[$key])) {
                $transferArray[$key] = null;
            }
        }
        if (isset($transferArray['email_daytime']) && $transferArray['email_daytime'] !== null) {
            $transferArray['email_daytime'] = sprintf('%s:00', $transferArray['email_daytime']);
        }
        if (isset($transferArray['ftp_daytime']) && $transferArray['ftp_daytime'] !== null) {
            $transferArray['ftp_daytime'] = sprintf('%s:00', $transferArray['ftp_daytime']);
        }

        return $transferArray;
    }

    /**
     * @param array $transferArray
     *
     * @return int
     */
    private function insertTransferArray($transferArray)
    {
        $sql = "INSERT INTO `report_transfer` (report_id, ftp_active, ftp_passive, ftp_type, ftp_host, ftp_port, ftp_user,
                               ftp_password, ftp_interval_mode, ftp_interval_value, ftp_daytime, ftp_format,
                               ftp_filename, email_active, email_recipient, email_subject, email_interval_mode,
                               email_interval_value, email_daytime, email_format, email_filename, url_format, url_begin, url_end,
                               url_address, api_active, api_account_id, api_format, url_token) 
                VALUES (:report_id, :ftp_active, :ftp_passive, :ftp_type, :ftp_host, :ftp_port, :ftp_user, :ftp_password,
                        :ftp_interval_mode, :ftp_interval_value, :ftp_daytime, :ftp_format, :ftp_filename,
                        :email_active, :email_recipient, :email_subject, :email_interval_mode, :email_interval_value,
                        :email_daytime, :email_format, :email_filename, :url_format, :url_begin, :url_end, :url_address, :api_active,
                        :api_account_id, :api_format, '')";
        $values = $this->sanitizeTransferDateTimes($transferArray);
        $this->db->fetchAffected($sql, $values);

        return $this->db->lastInsertId();
    }

    /**
     * @param array $transferArray
     *
     * @return int
     */
    private function updateTransferArray($transferArray)
    {
        $sql = 'UPDATE `report_transfer` SET 
                    report_id = :report_id,
                    ftp_active = :ftp_active,
                    ftp_passive = :ftp_passive,
                    ftp_type = :ftp_type,
                    ftp_host = :ftp_host,
                    ftp_port = :ftp_port,
                    ftp_user = :ftp_user,
                    ftp_password = :ftp_password,
                    ftp_interval_mode = :ftp_interval_mode,
                    ftp_interval_value = :ftp_interval_value, 
                    ftp_daytime = :ftp_daytime, 
                    ftp_format = :ftp_format,
                    ftp_filename = :ftp_filename,
                    email_active = :email_active,
                    email_recipient = :email_recipient,
                    email_subject = :email_subject,
                    email_interval_mode = :email_interval_mode,
                    email_interval_value = :email_interval_value,
                    email_daytime = :email_daytime,
                    email_format = :email_format,
                    email_filename = :email_filename,
                    url_format = :url_format,
                    url_begin = :url_begin, 
                    url_end = :url_end,
                    url_address = :url_address,
                    api_active = :api_active,
                    api_account_id = :api_account_id, 
                    api_format = :api_format
                    WHERE id=:id';
        $values = $this->sanitizeTransferDateTimes($transferArray);
        $affectedRows = $this->db->fetchAffected($sql, $values);
        if ($affectedRows > 0) {
            return (int)$values['id'];
        }

        return 0;
    }

    /**
     * @param array $reportUserArray
     *
     * @return int
     */
    private function insertReportUserArray($reportUserArray)
    {
        $sql = 'INSERT INTO `report_user` (report_id, user_id, name, chart_enabled, file_enabled,
                                            menu_enabled, tab_enabled)
                                VALUES (:report_id, :user_id, :name, :chart_enabled, :file_enabled,
                                            :menu_enabled, :tab_enabled)';
        $this->db->fetchAffected($sql, $reportUserArray);

        return $this->db->lastInsertId();
    }

    /**
     * @param array $reportUserArray
     *
     * @return int
     */
    private function updateReportUserArray($reportUserArray)
    {
        $username = $reportUserArray['name'];
        unset($reportUserArray['name']);
        $reportUserArray['userName'] = $username;
        $sql = 'UPDATE `report_user` SET
                    report_id = :report_id,
                    user_id = :user_id,
                         name = :userName,
                         chart_enabled = :chart_enabled,
                         file_enabled = :file_enabled,
                         menu_enabled = :menu_enabled,
                         tab_enabled = :tab_enabled
                WHERE id = :id';
        $affectedRows = $this->db->fetchAffected($sql, $reportUserArray);
        if ($affectedRows > 0) {
            return (int)$reportUserArray['id'];
        }

        return 0;
    }

    /**
     * @param array $shareArray
     *
     * @return int
     */
    private function insertShareArray($shareArray)
    {
        $sql = 'INSERT INTO `report_share` (report_id, chart_public, chart_axislabel, chart_dateformat, chart_type,
                            chart_interval_value, chart_interval_mode, file_public, file_pdf_enabled, file_csv_enabled,
                            file_xls_enabled, menu_public, menu_doctype, menu_label, menu_format, tab_public,
                            tab_module, tab_label, tab_position, tab_action, 
                            chart_x_column, data_columns,chart_group_column) 
                VALUES (:report_id, :chart_public, :chart_axislabel, :chart_dateformat, :chart_type,
                            :chart_interval_value, :chart_interval_mode, :file_public, :file_pdf_enabled, :file_csv_enabled,
                            :file_xls_enabled, :menu_public, :menu_doctype, :menu_label, :menu_format, :tab_public,
                            :tab_module, :tab_label, :tab_position, :tab_action,
                            :chart_x_column, :data_columns,:chart_group_column)';
        $values = $this->sanitizeTransferDateTimes($shareArray);
        $this->db->fetchAffected($sql, $values);

        return $this->db->lastInsertId();
    }

    /**
     * @param array $shareArray
     *
     * @return int
     */
    private function updateShareArray($shareArray)
    {
        $sql = 'UPDATE `report_share` SET 
                    report_id = :report_id,     
                    chart_public = :chart_public,
                    chart_axislabel = :chart_axislabel,
                    chart_dateformat = :chart_dateformat,
                    chart_type = :chart_type,
                    chart_x_column = :chart_x_column,
                    data_columns = :data_columns,
                    chart_group_column = :chart_group_column,
                    chart_interval_value = :chart_interval_value,
                    chart_interval_mode = :chart_interval_mode,
                    file_public = :file_public,
                    file_pdf_enabled = :file_pdf_enabled,
                    file_csv_enabled = :file_csv_enabled,
                    file_xls_enabled = :file_xls_enabled,
                    menu_public = :menu_public,
                    menu_doctype = :menu_doctype,
                    menu_label = :menu_label,
                    menu_format = :menu_format,
                    tab_public = :tab_public,
                    tab_module = :tab_module,
                    tab_action = :tab_action,
                    tab_label = :tab_label,
                    tab_position = :tab_position
                    WHERE id=:id';
        $values = $shareArray;
        $affectedRows = $this->db->fetchAffected($sql, $values);
        if ($affectedRows > 0) {
            return (int)$values['id'];
        }

        return 0;
    }

    /**
     * Finds copies of the specified name.
     *
     * @param string $name
     *
     * @param int    $ignoreId
     *
     * @return array
     */
    private function findReportCopyNames($name, $ignoreId = 0)
    {
        $sql = 'SELECT r.id, r.name FROM `report` AS `r` 
                WHERE (r.name LIKE :origName 
                   OR r.name LIKE :copyFormat1
                   OR r.name LIKE :copyFormat2)
                   AND r.id <> :ignoreId';
        $values = [
            'origName'    => $name,
            'copyFormat1' => sprintf('%s(%%)', $name),
            'copyFormat2' => sprintf('%s (%%)', $name),
            'ignoreId'    => $ignoreId,
        ];
        $resultArray = $this->db->fetchPairs($sql, $values);
        if (empty($resultArray)) {
            return [];
        }

        return $resultArray;
    }

    /**
     * @param $reportName
     *
     * @return mixed
     */
    private function getBaseName($reportName)
    {
        $baseName = $reportName;
        $isCopyName = preg_match('/^(.+\S)\s*\((\d+)\)$/', $reportName, $copyNameParts);
        if ($isCopyName === 1 && count($copyNameParts) > 1) {
            $baseName = $copyNameParts[1];
        }

        return $baseName;
    }
}
