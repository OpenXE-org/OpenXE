<?php
declare(strict_types = 1);

use Xentral\Components\Http\JsonResponse;
use Xentral\Components\Http\Request;
use Xentral\Components\Http\Response;
use Xentral\Components\Logger\LogLevel;
use Xentral\Modules\Log\Exception\InvalidArgumentException;
use Xentral\Modules\Log\Exception\InvalidLoglevelException;
use Xentral\Modules\Log\Service\DatabaseLogGateway;
use Xentral\Modules\Log\Service\DatabaseLogService;
use Xentral\Modules\Log\Service\LoggerConfigService;

class Log
{
    /** @var string MODULE_NAME */
    public const MODULE_NAME = 'Log';

    /** @var Application $app */
    protected $app;

    /** @var LoggerConfigService */
    protected $configService;

    /** @var DatabaseLogService $logService */
    protected $logService;

    /** @var Request $request */
    protected $request;

    /** @var array $javascript */
    public $javascript = [
        './classes/Modules/Log/www/js/delete_logs.js',
    ];

    /**
     * @param Application $app
     * @param bool        $intern
     */
    public function __construct($app, $intern = false)
    {
        $this->app = $app;
        if ($intern) {
            return;
        }
        $this->logService = $app->Container->get('DatabaseLogService');
        $this->configService = $app->Container->get('LoggerConfigService');
        $this->request = $app->Container->get('Request');
        $this->app->ActionHandlerInit($this);
        $this->app->ActionHandler("list", "LogList");
        $this->app->ActionHandler("settings", "LogSettings");
        $this->app->ActionHandler("deleteall", "LogDeleteAll");
        $this->app->ActionHandler("minidetail", "LogDumpMinidetail");
        $this->app->DefaultActionHandler("list");

        $this->app->ActionHandlerListen($app);
    }

    /**
     * @param Application $app
     * @param string      $name
     * @param array       $erlaubtevars
     *
     * @return array
     */
    public function TableSearch($app, $name, $erlaubtevars): array
    {
        switch ($name) {
            case 'log':
                $allowed['log'] = ['list'];

                $heading = [
                    '',
                    'ID',
                    'Zeit',
                    'Level',
                    'Aufrufart',
                    'Aufruf',
                    'Klasse',
                    'Funktion',
                    'Zeile',
                    'Nachricht',
                    '',
                ];
                $width = ['1%', '4%', '8%', '4%', '10%', '15%', '20%', '10%', '5%', '40%'];
                $findcols = [
                    'open',
                    'l.id',
                    'l.log_time',
                    'l.level',
                    'l.origin_type',
                    'l.origin_detail',
                    'l.class',
                    'l.method',
                    'l.line',
                    'l.message',
                    'l.id',
                ];
                $searchsql = [
                    'l.id',
                    'l.level',
                    'l.origin_type',
                    'l.origin_detail',
                    'l.class',
                    'l.method',
                    'l.line',
                    'l.message',
                ];
                $defaultorder = 2;
                $defaultorderdesc = 1;
                $menucol = 1;
                $moreinfo = true;
                $menu = '';

                // SQL statement
                $sql = "SELECT l.id,
                   '<img src=./themes/new/images/details_open.png class=details>' AS  `open`,
                   l.id,
                   DATE_FORMAT(l.log_time,'%d.%m.%Y %H:%i:%s') AS `log_time`,
                   l.level, l.origin_type, l.origin_detail, l.class, l.method, l.line, l.message, l.id
                   FROM `log` AS `l`";
                $fastcount = 'SELECT COUNT(l.id) FROM `log` AS `l`';
                break;
        }

        $erg = [];

        foreach ($erlaubtevars as $k => $v) {
            if (isset($$v)) {
                $erg[$v] = $$v;
            }
        }

        return $erg;
    }

    /**
     * @return void
     */
    public function LogDumpMinidetail(): void
    {
        if (!$this->app->Container->has('DatabaseLogGateway')) {
            $response = new Xentral\Components\Http\Response('', 500);
            $response->send();
            $this->app->ExitXentral();
        }
        /** @var DatabaseLogGateway $gateway */
        $gateway = $this->app->Container->get('DatabaseLogGateway');
        $id = $this->request->get->getInt('id', 0);
        if ($id < 1) {
            $response = new Response('', 404);
            $response->send();
            $this->app->ExitXentral();
        }
        $dump = $gateway->tryGetLogDump($id);
        if ($dump !== null) {
            $dump = sprintf('<div><p>Dump:</p><pre>%s</pre></div>', $dump);
        }
        $response = new Response($dump, 200);
        $response->send();
        $this->app->ExitXentral();
    }

    /**
     * deletes all existing log entries.
     *
     * @return void
     */
    public function LogDeleteAll(): void
    {
        if (!$this->request->post->getBool('delete')) {
            $response = new JsonResponse(
                ['error' => 'Unknown client error'],
                Response::HTTP_BAD_REQUEST
            );
            $response->send();
            $this->app->ExitXentral();
        }
        try {
            $this->logService->removeAllLogs();
        } catch (Exception $e) {
            $response = new JsonResponse(
                ['error' => 'Unknown server error'],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
            $response->send();
            $this->app->ExitXentral();
        }
        $response = new JsonResponse(['success' => true]);
        $response->send();
        $this->app->ExitXentral();
    }

    /**
     * @return void
     */
    public function LogList(): void
    {
        $this->LogMenu();
        $this->app->YUI->TableSearch('TAB1', 'log', 'show', '', '', basename(__FILE__), __CLASS__);
        $this->app->Tpl->Parse('PAGE', 'log_list.tpl');
    }

    /**
     * @return void
     */
    public function LogSettings(): void
    {
        $this->LogMenu();
        if ($this->request->post->has('submit')) {
            try {
                $this->saveSettings();
            } catch (InvalidArgumentException $e) {
                $this->app->Tpl->Add(
                    'MESSAGE',
                    sprintf('<div class="error">%s</div>', $e->getMessage())
                );
            }
        }
        $this->renderSettings();
        $this->app->Tpl->Parse('PAGE', 'log_settings.tpl');
    }

    /**
     * @return void
     */
    public function LogMenu(): void
    {
        $this->app->erp->Headlines('Logger');
        $this->app->erp->MenuEintrag('index.php?module=log&action=list', 'Zur&uuml;ck zur &Uuml;bersicht');
        $this->app->erp->MenuEintrag('index.php?module=log&action=list', '&Uuml;bersicht');
        $this->app->erp->MenuEintrag('index.php?module=log&action=settings', 'Einstellungen');
    }

    /**
     * @return void
     */
    public function Install(): void
    {
        $this->app->erp->CheckTable('log');
        $this->app->erp->CheckColumn('id','INT(11) UNSIGNED','log','NOT NULL AUTO_INCREMENT');
        $this->app->erp->CheckColumn('log_time','DATETIME','log');
        $this->app->erp->CheckColumn('level','VARCHAR(16)','log');
        $this->app->erp->CheckColumn('message','TEXT','log');
        $this->app->erp->CheckColumn('class','VARCHAR(255)','log');
        $this->app->erp->CheckColumn('method','VARCHAR(64)','log');
        $this->app->erp->CheckColumn('line','INT(11) UNSIGNED','log');
        $this->app->erp->CheckColumn('origin_type','VARCHAR(64)','log');
        $this->app->erp->CheckColumn('origin_detail','VARCHAR(255)','log');
        $this->app->erp->CheckColumn('dump','TEXT','log');

        $this->app->erp->CheckProzessstarter('Log Cleaner', 'uhrzeit', '', '2017-01-01 00:00:00', 'cronjob', 'log_cleaner', 1);
    }

    /**
     * @throws InvalidArgumentException
     */
    protected function saveSettings(): void
    {
        $level = $this->request->post->getAlpha('level', '');
        $this->configService->setLogLevel(strtolower($level));
    }

    /**
     * @return void
     */
    protected function renderSettings(): void
    {
        try {
            $level = $this->configService->getLogLevel();
        } catch (InvalidLoglevelException $e) {
            $level = LogLevel::ERROR;
        }
        $this->app->Tpl->Set(sprintf('%s_SELECTED', strtoupper($level)), 'selected');
    }
}
