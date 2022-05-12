<?php

namespace Xentral\Modules\Report;

use Xentral\Modules\Report\Data\ReportData;
use Xentral\Modules\Report\Data\ReportParameter;
use Xentral\Modules\Report\Data\ReportParameterCollection;
use Xentral\Modules\Report\Exception\UnresolvedParameterException;

class ReportResolveParameterService
{
    /** @var string CONTROL_TYPE_DATE */
    const CONTROL_TYPE_DATE = 'date';

    /** @var string CONTROL_TYPE_TEXT */
    const CONTROL_TYPE_TEXT = 'text';

    /** @var string CONTROL_TYPE_COMBOBOX */
    const CONTROL_TYPE_COMBOBOX = 'combobox';

    /** @var string CONTROL_TYPE_PROJECT */
    const CONTROL_TYPE_PROJECT = 'autocomplete_project';

    /** @var string CONTROL_TYPE_GROUP */
    const CONTROL_TYPE_GROUP = 'autocomplete_group';

    /** @var string CONTROL_TYPE_ADDRESS */
    const CONTROL_TYPE_ADDRESS = 'autocomplete_address';

    /** @var string CONTROL_TYPE_ARTICLE */
    const CONTROL_TYPE_ARTICLE = 'autocomplete_article';

    /** @var int $userId */
    private $userId;

    /** @var array $userProjects */
    private $userProjects;

    /** @var bool $userIsAdmin */
    private $userIsAdmin;

    /**
     * @param int           $userId
     * @param array         $userProjects
     * @param bool          $userIsAdmin
     */
    public function __construct($userId, $userProjects, $userIsAdmin)
    {
        $this->userId = $userId;
        $this->userProjects = $userProjects;
        $this->userIsAdmin = $userIsAdmin;
    }

    /**
     * @param ReportData $report
     * @param array $parameterValues
     *
     * @return string compiled sql query
     */
    public function resolveParameters(ReportData $report, $parameterValues = [])
    {
        $compiledQuery = $this->resolveEnvironmentVariables($report);
        $resolvedReport = $this->resolveInputParameters($report, $parameterValues);
        $parameters = $resolvedReport->getParameters();
        if ($parameters !== null) {
            foreach ($parameters as $param) {
                $value = $param->getValue();
                $pattern = sprintf('/\{%s\}/', mb_strtoupper($param->getVarname()));
                $compiledQuery = preg_replace($pattern, $value, $compiledQuery);
            }
        }

        preg_match_all('/{([^{}]+)}/', $compiledQuery, $matches);
        if (!empty($matches[1])) {
            throw new UnresolvedParameterException(sprintf('unresolved Variable "%s"', $matches[1][0]));
        }

        return $compiledQuery;
    }

    /**
     * @param ReportData $report
     * @param array      $parameterValues
     *
     * @return ReportData
     */
    public function resolveInputParameters(ReportData $report, $parameterValues = [])
    {
        $params = $report->getParameters();
        if ($params === null || count($params) < 1) {
            return $report;
        }

        $resolvedParams = [];
        foreach ($params as $param) {
            $varname = strtolower($param->getVarname());
            foreach ($parameterValues as $varkey => $varvalue) {
                if ($varname === strtolower($varkey)) {
                    $resolvedValue = $this->resolveParameterValue($param, $varvalue);
                    $param->setTemporaryValue($resolvedValue);
                }
            }
            $resolvedParams[] = $param;
        }
        $report->setParameters(new ReportParameterCollection($resolvedParams));

        return $report;
    }

    /**
     * @param ReportData $report
     *
     * @return string
     */
    public function resolveEnvironmentVariables(ReportData $report)
    {
        $variables = $this->getEnvironMentVariables($report);
        $compiledQuery = $report->getSqlQuery();
        foreach ($variables as $key => $value) {
            $pattern = sprintf('/\{%s\}/', $key);
            $compiledQuery = preg_replace($pattern, $value, $compiledQuery);
        }

        return $compiledQuery;
    }

    /**
     * @param ReportData $report
     *
     * @return array
     */
    public function getEnvironmentVariables(ReportData $report)
    {
        $variables = [];
        if ($this->userId > 0) {
            $variables['USER_ID'] = $this->userId;
            $variables['USER_PROJECTS'] = sprintf('(%s)', implode(', ', $this->userProjects));
            $variables['USER_ADMIN'] = 0;
            if ($this->userIsAdmin === true) {
                $variables['USER_ADMIN'] = 1;
            }
        }
        $variables['REPORT_PROJECT'] = $report->getProjectId();

        return $variables;
    }

    /**
     * @param $sql
     *
     * @return string[]
     */
    public function findInvalidVariableNames($sqlStatement)
    {
        if (
            !preg_match_all('/{([^{}]+)}/', $sqlStatement, $matches)
            || count($matches) < 2
        ) {
            return [];
        }

        $errors = [];
        foreach ($matches[1] as $varname) {
            if(!preg_match('/^[A-Z_0-9]+$/', $varname)) {
                $errors[] = $varname;
            }
        }

        return $errors;
    }

    /**
     * @param ReportParameter $parameter
     * @param mixed           $value
     *
     * @throws UnresolvedParameterException
     *
     * @return mixed
     */
    private function resolveParameterValue(ReportParameter $parameter, $value)
    {
        $control_type = $parameter->getControlType();
        if ($value === null) {
            return null;
        }

        switch ($control_type) {

            case self::CONTROL_TYPE_PROJECT:
                return $this->resolveProject($value);
                break;

            case self::CONTROL_TYPE_GROUP:
                return $this->resolveGroup($value);
                break;

            case self::CONTROL_TYPE_ADDRESS:
                return $this->resolveAddress($value);
                break;

            case self::CONTROL_TYPE_ARTICLE:
                return $this->resolveArticle($value);
                break;
        }

        return $value;
    }

    /**
     * @param string $input
     *
     * @return string
     */
    private function resolveProject($input)
    {
        $split = explode(' ', $input);
        if (count($split) < 1) {
            throw new UnresolvedParameterException('project parameter format incorrect');
        }

        return $split[0];
    }

    /**
     * @param string $input
     *
     * @return string
     */
    private function resolveGroup($input)
    {
        $split = explode(' ', $input);
        if (count($split) < 2) {
            throw new UnresolvedParameterException('group parameter format incorrect');
        }

        return $split[count($split)-1];
    }

    /**
     * @param string $input
     *
     * @return string
     */
    private function resolveAddress($input)
    {
        $split = explode(' ', $input);
        if (count($split) < 1) {
            throw new UnresolvedParameterException('address parameter format incorrect');
        }

        return $split[0];
    }

    /**
     * @param string $input
     *
     * @return string
     */
    private function resolveArticle($input)
    {
        $split = explode(' ', $input);
        if (count($split) < 2) {
            throw new UnresolvedParameterException('article parameter format incorrect');
        }

        return $split[0];
    }
}
