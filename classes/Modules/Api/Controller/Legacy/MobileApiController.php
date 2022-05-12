<?php

namespace Xentral\Modules\Api\Controller\Legacy;

use DateInterval;
use DateTime;
use DateTimeInterface;
use Exception;
use Xentral\Components\Database\Database;
use Xentral\Components\Http\Request;
use Xentral\Modules\Api\Controller\Version1\AbstractController;
use Xentral\Modules\Api\Converter\Converter;
use Xentral\Modules\Api\Dashboard\WidgetData;
use Xentral\Modules\Api\Dashboard\WidgetResult;
use Xentral\Modules\Api\Exception\BadRequestException;
use Xentral\Modules\Api\Exception\InvalidArgumentException;
use Xentral\Modules\Api\LegacyBridge\LegacyApplication;

class MobileApiController extends AbstractController
{
    /** @var LegacyApplication $app */
    private $app;

    /**
     * @param LegacyApplication $app
     * @param Converter         $converter
     * @param Database          $database
     * @param Request           $request
     */
    public function __construct(LegacyApplication $app, Converter $converter, Database $database, Request $request)
    {
        parent::__construct(null, $database, $converter, $request, null);
        $this->app = $app;
    }

    /**
     * controller for dashboard api call
     *
     * uses optional GET request parameter 'date'
     *
     * @throws Exception
     */
    public function dashboardAction()
    {
        $today = new DateTime('now');
        $interval = (int)$this->request->get->get('interval');
        $mode = $this->request->get->get('mode');
        if (!in_array($mode, ['month', 'week', 'year'])) {
            $mode = 'day';
        }
        if ($interval <= 0) {
            switch ($mode) {
                case 'year':
                    $interval = 10;
                    break;
                case 'month':
                    $interval = 12;
                    break;
                case 'week':
                default:
                    $interval = 14;
                    break;
            }
        }
        $requestDate = $this->request->get->get('date');
        if ($requestDate !== null && !$this->isDate($requestDate)) {
            throw new BadRequestException('Bad request: parameter \'date\' expected format YYYY-mm-dd');
        }

        if ($this->isDate($requestDate)) {
            $today = new DateTime($requestDate);
        }
        $yesterday = new DateTime($today->format('Y-m-d'));
        $yesterday = $yesterday->sub(new DateInterval('P1D'));
        $lastYear = new DateTime($today->format('Y'));
        $lastYear = $lastYear->sub(new DateInterval('P1Y'));

        $result = new WidgetResult([]);
        //Dashboard mainpage
        $result->addData($this->getOrdersCountWidget($today, $yesterday));
        $result->addData($this->getTurnoverWidget($today, $yesterday));
        $result->addData($this->getDispatchWidget($today, $yesterday));
        $result->addData($this->getOrderValueWidget($today, $yesterday));
        $result->addData($this->getTwoWeeksTurnoverWidget($today, $interval, $mode));
        $result->addData($this->getNewCustomerWidget($today, $yesterday));
        $result->addData($this->getOpenTicketsWidget());
        //financial data page
        $result->addData(
            new WidgetData(
                'turnover_current',
                WidgetData::WIDGET_TYPE_CONTRAST_BIG,
                'Umsatz aktueller Monat (netto)',
                ['current' => $this->getTurnoverThisMonth(), 'previous' => $this->getTurnoverLastMonth()],
                'cashflow',
                '€',
                WidgetData::FORMAT_CURRENCY
            )
        );
        $result->addData(
            new WidgetData(
                'turnover_lastmonth',
                WidgetData::WIDGET_TYPE_CONTRAST_BIG,
                'Umsatz letzter Monat (netto)',
                ['current' => $this->getTurnoverLastMonth(), 'previous' => $this->getTurnoverBeforeLastMonth()],
                'cashflow',
                '€',
                WidgetData::FORMAT_CURRENCY
            )
        );
        $result->addData(
            new WidgetData(
                'turnover_beforelastmonth',
                WidgetData::WIDGET_TYPE_SIMPLE_BIG,
                'Umsatz vorletzter Monat (netto)',
                ['value' => $this->getTurnoverBeforeLastMonth()],
                'cashflow',
                '€',
                WidgetData::FORMAT_CURRENCY
            )
        );
        $result->addData(
            new WidgetData(
                'liability_open',
                WidgetData::WIDGET_TYPE_SIMPLE_BIG,
                'Offene Verbindlichkeiten (brutto)',
                ['value' => $this->getOpenLiabilies()],
                'cashflow',
                '€',
                WidgetData::FORMAT_CURRENCY
            )
        );
        $result->addData(
            new WidgetData(
                'orders_open',
                WidgetData::WIDGET_TYPE_SIMPLE_BIG,
                'Offene Aufträge (netto)',
                ['value' => $this->getOpenOrders()],
                'cashflow',
                '€',
                WidgetData::FORMAT_CURRENCY
            )
        );
        $result->addData(
            new WidgetData(
                'dunning_current',
                WidgetData::WIDGET_TYPE_SIMPLE_BIG,
                'Mahnwesen (brutto)',
                ['value' => $this->getDunning()],
                'cashflow',
                '€',
                WidgetData::FORMAT_CURRENCY
            )
        );
        $result->addData(
            new WidgetData(
                'timetrack_current',
                WidgetData::WIDGET_TYPE_SIMPLE_BIG,
                'Zeit Gebucht',
                ['value' => $this->getTimeTracking()],
                'customer',
                '',
                WidgetData::FORMAT_HOURS
            )
        );
        $result->addData(
            new WidgetData(
                'subscription_nextmonth',
                WidgetData::WIDGET_TYPE_SIMPLE_BIG,
                'Abolauf nächsten Monat (brutto)',
                ['value' => $this->getSubscriptionRun()],
                'cashflow',
                '€',
                WidgetData::FORMAT_CURRENCY
            )
        );
        $result->addData(
            new WidgetData(
                'accounts_total_current',
                WidgetData::WIDGET_TYPE_SIMPLE_BIG,
                'Bankkonten Gesamt',
                ['value' => $this->getAccountsTotal()],
                'cashflow',
                '€',
                WidgetData::FORMAT_CURRENCY
            )
        );
        $result->addData(
            new WidgetData(
                'turnover_year_current',
                WidgetData::WIDGET_TYPE_CONTRAST_BIG,
                'Gesamtumsatz laufendes Jahr (netto)',
                ['current' => $this->getTurnoverByYear($today), 'previous' => $this->getTurnoverByYear($lastYear)],
                'cashflow',
                '€',
                WidgetData::FORMAT_CURRENCY
            )
        );

        return $this->sendResult($result);
    }

    /**
     * returns chart data for number of orders
     *
     * @param DateTimeInterface $currentDay  today
     * @param DateTimeInterface $previousDay yesterday
     *
     * @return WidgetData chart data
     */
    protected function getOrdersCountWidget(DateTimeInterface $currentDay, DateTimeInterface $previousDay)
    {
        $currentNumber = (int)$this->getOrdersCountByDay($currentDay);
        $previousNumber = (int)$this->getOrdersCountByDay($previousDay);

        $widget = new WidgetData(
            'order_count',
            WidgetData::WIDGET_TYPE_CONTRAST,
            'Aufträge',
            ['current' => $currentNumber, 'previous' => $previousNumber],
            'basket'
        );

        return $widget;
    }

    protected function getTurnoverWidget(DateTimeInterface $currentDay, DateTimeInterface $previousDay)
    {
        return new WidgetData(
            'turnover_day',
            WidgetData::WIDGET_TYPE_CONTRAST,
            'Umsatz (heute)',
            ['current' => $this->getTurnoverByDay($currentDay), 'previous' => $this->getTurnoverByDay($previousDay)],
            'euro',
            '€',
            WidgetData::FORMAT_CURRENCY
        );
    }

    /**
     * returns chart data for new customers
     *
     * @param DateTimeInterface $currentDay  today
     * @param DateTimeInterface $previousDay yesterday
     *
     * @return WidgetData chart data
     */
    protected function getNewCustomerWidget(DateTimeInterface $currentDay, DateTimeInterface $previousDay)
    {
        $currentNumber = (int)$this->getNewCustomersByDay($currentDay);
        $previousNumber = (int)$this->getNewCustomersByDay($previousDay);

        $widget = new WidgetData(
            'customer_new',
            WidgetData::WIDGET_TYPE_CONTRAST,
            'Neukunden',
            ['current' => $currentNumber, 'previous' => $previousNumber],
            'customer'
        );

        return $widget;
    }

    protected function getOpenTicketsWidget()
    {
        return new WidgetData(
            'tickets',
            WidgetData::WIDGET_TYPE_SIMPLE,
            'Offene Tickets',
            ['value' => $this->getOpenTicketCount()],
            'ticket'
        );
    }

    /**
     * returns chart data for dispatched packages
     *
     * @param DateTimeInterface $currentDay  today
     * @param DateTimeInterface $previousDay yesterday
     *
     * @return WidgetData chart data
     */
    protected function getDispatchWidget(DateTimeInterface $currentDay, DateTimeInterface $previousDay)
    {
        $currentNumber = (int)$this->getDispatchCountByDay($currentDay);
        $previousNumber = (int)$this->getDispatchCountByDay($previousDay);

        $widget = new WidgetData(
            'dispatch_package',
            WidgetData::WIDGET_TYPE_CONTRAST,
            'Pakete',
            ['current' => $currentNumber, 'previous' => $previousNumber],
            'packages'
        );

        return $widget;
    }

    protected function getOrderValueWidget(DateTimeInterface $today, DateTimeInterface $yesterday)
    {
        return new WidgetData(
            'order_value',
            WidgetData::WIDGET_TYPE_CONTRAST,
            'Aufträge Heute',
            ['current' => $this->getOrderValueByDay($today), 'previous' => $this->getOrderValueByDay($yesterday)],
            'euro',
            '€',
            WidgetData::FORMAT_CURRENCY
        );
    }

    /**
     * returns chart data for 14 days turnover
     *
     * @param DateTimeInterface $currentDay
     * @param int               $interval
     * @param string            $mode
     *
     * @throws Exception
     * @return WidgetData chart data
     */
    protected function getTwoWeeksTurnoverWidget(DateTimeInterface $currentDay, $interval = 0, $mode = 'day')
    {

        $dateString = $currentDay->format('Y-m-d');
        switch ($mode) {
            case 'year':
                $modeName = 'Jahre';
                if ($interval <= 0) {
                    $interval = 10;
                }
                $dayTo = new DateTime((new DateTime($dateString))->format('Y-12-31'));
                $dayFrom = new DateTime((new DateTime($dateString))->format('Y-01-01'));
                $dayFrom = $dayFrom->sub(new DateInterval(sprintf('P%dY', $interval - 1)));
                break;
            case 'month':
                $modeName = 'Monate';
                if ($interval <= 0) {
                    $interval = 12;
                }
                $dayTo = (new DateTime((new DateTime($dateString))
                    ->format('Y-m-01')))
                    ->add(new DateInterval('P1M'))
                    ->sub(new DateInterval('P1D'));
                $dayFrom = new DateTime((new DateTime($dateString))->format('Y-m-01'));
                $dayFrom = $dayFrom->sub(new DateInterval(sprintf('P%dM', $interval - 1)));
                break;
            case 'week':
                $modeName = 'Wochen';
                if ($interval <= 0) {
                    $interval = 14;
                }
                $dayTo = new DateTime($dateString);
                $weekDay = $dayTo->format('N');
                if ($weekDay < 7) {
                    $dayTo->add(new DateInterval((sprintf('P%dD', 7 - $weekDay))));
                }
                $dayFrom = new DateTime($dayTo->format('Y-m-d'));
                $dayFrom = $dayFrom->sub(new DateInterval(sprintf('P%dD', $interval * 7 - 1)));
                break;
            default:
                $modeName = 'Tage';
                if ($interval <= 0) {
                    $interval = 14;
                }
                $dayTo = new DateTime($dateString);
                $dayFrom = new DateTime($dateString);
                $dayFrom = $dayFrom->sub(new DateInterval(sprintf('P%dD', $interval - 1)));
                break;
        }


        $data = $this->getTrunoverByDays($dayFrom, $dayTo, $mode);

        $widget = new WidgetData(
            'turnover_period',
            WidgetData::WIDGET_TYPE_BARCHART,
            sprintf('Umsatz (%d %s)', $interval, $modeName),
            $data,
            'euro',
            '€',
            WidgetData::FORMAT_CURRENCY
        );

        return $widget;
    }

    /**
     * Returns number of orders created on specific date
     *
     * @param DateTimeInterface $date
     *
     * @return integer
     */
    protected function getOrdersCountByDay(DateTimeInterface $date)
    {
        $dateFormatted = $date->format('Y-m-d');
        if (!$this->isDate($dateFormatted)) {
            throw new InvalidArgumentException('Invalid date format.');
        }
        $sql = 'SELECT COUNT(a.id) AS anzahl FROM auftrag AS a WHERE a.datum = :dateFormatted';
        $values = ['dateFormatted' => $dateFormatted];
        $result = $this->db->fetchRow($sql, $values);

        return (int)$result['anzahl'];
    }

    /**
     * Returns total revenue of specific day
     *
     * @param DateTimeInterface $date
     *
     * @return double
     */
    protected function getOrderValueByDay(DateTimeInterface $date)
    {
        $dateFormatted = $date->format('Y-m-d');
        $sql = "SELECT SUM(a.gesamtsumme) AS `ordervalue`
                FROM auftrag AS a
                WHERE a.datum = :dateFormatted AND a.status!='angelegt'";
        $values = ['dateFormatted' => $dateFormatted];
        $result = $this->db->fetchRow($sql, $values);

        return (float)$result['ordervalue'];
    }

    /**
     * Returns number of customers who placed their first order on specific date
     *
     * @param DateTimeInterface $date
     *
     * @return integer
     */
    protected function getNewCustomersByDay(DateTimeInterface $date)
    {
        $dateFormatted = $date->format('Y-m-d');
        if (!$this->isDate($dateFormatted)) {
            throw new InvalidArgumentException('Invalid date format.');
        }

        $sql = "SELECT Count(DISTINCT adr.name) AS neukunden
                    FROM adresse AS adr JOIN auftrag AS auf
                    ON adr.id = auf.adresse
                    WHERE adr.id NOT IN
                        (SELECT DISTINCT a.id
                        FROM adresse AS a RIGHT JOIN auftrag AS au
                        ON a.id = au.adresse
                        WHERE au.status<>'angelegt' AND au.datum <> :dateFormatted AND au.id IS NOT NULL
                        );";
        $values = ['dateFormatted' => $dateFormatted];
        $result = $this->db->fetchRow($sql, $values);

        return (int)$result['neukunden'];
    }

    /**
     * Returns number of packeges dispatched on specific date
     *
     * @param DateTimeInterface $date
     *
     * @return integer
     */
    protected function getDispatchCountByDay(DateTimeInterface $date)
    {
        $dateFormatted = $date->format('Y-m-d');
        if (!$this->isDate($dateFormatted)) {
            throw new InvalidArgumentException('Invalid date format.');
        }
        $sql = 'SELECT COUNT(v.id) AS anzahlpakete FROM versand AS v WHERE v.versendet_am = :dateFormatted';
        $values = ['dateFormatted' => $dateFormatted];
        $result = $this->db->fetchRow($sql, $values);

        return (int)$result['anzahlpakete'];
    }

    protected function getOpenTicketCount()
    {
        return (float)$this->app->erp->AnzahlOffeneTickets(false);
    }

    /**
     * Returns true if specific string represents a date.
     *
     * Accepted date format 'Y-m-d'
     *
     * @example isDate('2019-08-23') -> true
     *
     * @param string $dateString
     *
     * @return bool true=string represents a date
     */
    protected function isDate($dateString)
    {
        $date = (string)$dateString;
        if (preg_match('/^[1-9]\d{3}-\d{2}-\d{2}$/', $date)) {
            return true;
        }

        return false;
    }

    /**
     * @return float
     */
    protected function getCashValues($key)
    {
        $obj = $this->app->loadModule('managementboard');
        if (empty($obj)) {
            return null;
        }

        $value = $obj->getCashValues($key);

        return $value;
    }

    /**
     * @param DateTimeInterface $dateFrom
     * @param DateTimeInterface $dateTo
     * @param string            $mode
     *
     * @throws Exception
     * @return array
     */
    private function getTrunoverByDays(DateTimeInterface $dateFrom, DateTimeInterface $dateTo, $mode = 'day')
    {
        if ($dateFrom > $dateTo) {
            throw new BadRequestException('Bad request: parameter \'dateFrom\' is later than parameter \'dateTo\'');
        }

        switch ($mode) {
            case 'year':
                $formatDb = '%Y';
                $formatPhp = 'Y';
                break;
            case 'month':
                $formatDb = '%m/%Y';
                $formatPhp = 'm/Y';
                break;
            case 'week':
                $formatDb = '%v/%x';
                $formatPhp = 'W/o';
                break;
            default:
                $formatDb = '%Y-%m-%d';
                $formatPhp = 'Y-m-d';
                break;
        }

        $dateFormattedFrom = $dateFrom->format('Y-m-d');
        $dateFormattedTo = $dateTo->format('Y-m-d');
        $values = [
            'dateFormattedFrom' => $dateFormattedFrom,
            'dateFormattedTo'   => $dateFormattedTo,
        ];

        $sqlInvoices = sprintf(
            "SELECT DATE_FORMAT(r.datum,'%s') AS `date`, sum(r.umsatz_netto) AS `commitment`
                FROM rechnung AS r 
                WHERE DATE_FORMAT(r.datum,'%%Y-%%m-%%d') >= :dateFormattedFrom
                  AND DATE_FORMAT(r.datum,'%%Y-%%m-%%d') <= :dateFormattedTo
                  AND r.status!='angelegt'
                GROUP BY DATE_FORMAT(r.datum,'%s')",
            $formatDb, $formatDb
        );
        $resultInvoices = $this->db->fetchPairs($sqlInvoices, $values);
        $sqlReturnOrders = sprintf(
            "SELECT DATE_FORMAT(g.datum,'%s') AS `date`, sum(g.umsatz_netto) AS `credit`
                FROM gutschrift AS g 
                WHERE DATE_FORMAT(g.datum,'%%Y-%%m-%%d') >= :dateFormattedFrom
                  AND DATE_FORMAT(g.datum,'%%Y-%%m-%%d') <= :dateFormattedTo
                  AND g.status!='angelegt'
                GROUP BY DATE_FORMAT(g.datum,'%s')",
            $formatDb, $formatDb
        );
        $resultReturnOrders = $this->db->fetchPairs($sqlReturnOrders, $values);

        $day = new DateTime($dateFormattedFrom);
        $return = [];
        while ($day <= $dateTo) {
            $dayFormated = $day->format($formatPhp);
            $return[$dayFormated] =
                (empty($resultInvoices[$dayFormated]) ? 0.0 : $resultInvoices[$dayFormated])
                - (empty($resultReturnOrders[$dayFormated]) ? 0.0 : $resultReturnOrders[$dayFormated]);

            switch ($mode) {
                case 'year':
                    $day = $day->add(new DateInterval('P1Y'));
                    break;
                case 'month':
                    $day = $day->add(new DateInterval('P1M'));
                    break;
                case 'week':
                    $day = $day->add(new DateInterval('P7D'));
                    break;
                default:
                    $day = $day->add(new DateInterval('P1D'));
                    break;
            }

        }

        return $return;
    }

    /**
     * @param DateTimeInterface $date
     *
     * @return float
     */
    private function getTurnoverByDay(DateTimeInterface $date)
    {
        $dateFormatted = $date->format('Y-m-d');
        $values = ['dateFormatted' => $dateFormatted];
        $sql = "SELECT sum(r.umsatz_netto) AS `commitment`
                FROM rechnung AS r 
                WHERE DATE_FORMAT(r.datum,'%Y-%m-%d')=:dateFormatted AND r.status!='angelegt'";
        $result = $this->db->fetchRow($sql, $values);
        if (empty($result)) {
            return 0.0;
        }
        $commitment = (float)$result['commitment'];

        $sql = "SELECT sum(g.umsatz_netto) AS `credit`
                FROM gutschrift AS g 
                WHERE DATE_FORMAT(g.datum,'%Y-%m-%d')=:dateFormatted AND g.status!='angelegt'";
        $result = $this->db->fetchRow($sql, $values);
        if (empty($result)) {
            return 0.0;
        }
        $credit = (float)$result['credit'];

        return $commitment - $credit;
    }

    /**
     * @param DateTimeInterface $date
     *
     * @return float
     */
    private function getTurnoverByYear(DateTimeInterface $date)
    {
        $dateFormatted = $date->format('Y');
        $values = ['dateFormatted' => $dateFormatted];
        $sql = "SELECT sum(r.umsatz_netto) AS `commitment`
                FROM rechnung AS r 
                WHERE DATE_FORMAT(r.datum,'%Y')=:dateFormatted AND r.status!='angelegt'";
        $result = $this->db->fetchRow($sql, $values);
        if (empty($result)) {
            return 0.0;
        }
        $commitment = (float)$result['commitment'];

        $sql = "SELECT sum(g.umsatz_netto) AS `credit`
                FROM gutschrift AS g 
                WHERE DATE_FORMAT(g.datum,'%Y')=:dateFormatted AND g.status!='angelegt'";
        $result = $this->db->fetchRow($sql, $values);
        if (empty($result)) {
            return 0.0;
        }
        $credit = (float)$result['credit'];

        return $commitment - $credit;
    }

    /**
     * @return float
     */
    private function getTurnoverThisMonth()
    {
        return (float)$this->getCashValues('13.1') - (float)$this->getCashValues('13.2');
    }

    /**
     * @return float
     */
    private function getTurnoverLastMonth()
    {
        return (float)$this->getCashValues('17.1') - (float)$this->getCashValues('17.2');
    }

    /**
     * @return float
     */
    private function getTurnoverBeforeLastMonth()
    {
        return (float)$this->getCashValues('21.1') - (float)$this->getCashValues('21.2');
    }

    /**
     * @return float
     */
    private function getOpenLiabilies()
    {
        return (float)$this->getCashValues(9);
    }

    /**
     * @return float
     */
    private function getOpenOrders()
    {
        return (float)$this->getCashValues(10);
    }

    /**
     * @return float
     */
    private function getDunning()
    {
        return (float)$this->getCashValues(11);
    }

    /**
     * @return float
     */
    private function getTimeTracking()
    {
        return (float)$this->app->DB->Select(
            "SELECT sum(TIMESTAMPDIFF(HOUR,von,bis)) 
                    FROM zeiterfassung 
                    WHERE DATE_FORMAT(von,'%m-%Y') = DATE_FORMAT(NOW(),'%m-%Y')"
        );
    }

    /**
     * @return float
     */
    private function getSubscriptionRun()
    {
        $obj = $this->app->erp->LoadModul('rechnungslauf');
        $value = 0.0;
        if ($obj) {
            $value = (float)$obj->RechnungslaufRechnungslauf(true);
        }

        return $value;
    }

    /**
     * @return float
     */
    private function getAccountsTotal()
    {
        return (float)$this->getCashValues(25);
    }
}
