<?php

namespace Xentral\Core\ErrorHandler;

final class ErrorPageRenderer
{
    /** @var array REQUIRED_PHP_EXTENSIONS */
    const REQUIRED_PHP_EXTENSIONS = [
        'PDO', 'mysqli', 'mysqlnd', 'mbstring', 'curl', 'xml', 'zip', 'stream_socket_enable_crypto'
    ];

    /** @var ErrorPageData $data */
    private $data;

    /**
     * @param ErrorPageData $data
     */
    public function __construct(ErrorPageData $data)
    {
        $this->data = $data;
    }

    /**
     * @return string
     */
    public function renderErrorPage()
    {
        $data = $this->data->getData();
        $title = $this->data->getTitle();
        $content = $this->renderPageHeader();

        $content .= '<table>';
        $content .= '<tr><td colspan="2" id="headline">';
        $content .= '<h1>' . htmlspecialchars($title, ENT_QUOTES) . '</h1>';
        $content .= '<h2>' . htmlspecialchars($data['exception']['message'], ENT_QUOTES) . '</h2>';
        $content .= '</td></tr>';
        $content .= '<tr>';
        $content .= '<td width="20%" id="side">' . $this->renderInformationData($data['information']) . '</td>';
        $content .= '<td width="80%" id="main">' . $this->renderExceptionData($data['exception']) . '</td>';
        $content .= '</tr></table>';

        $content .= $this->renderPageFooter();

        return $content;
    }

    /**
     * @return string
     */
    private function renderPageHeader()
    {
        return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Unerwarteter Fehler</title>
  <style type="text/css">
    html { padding: 0; margin: 0; }
    body { font-family: BlinkMacSystemFont, -apple-system, Segoe UI, Roboto, Oxygen, Ubuntu, Cantarell, Fira Sans, Droid Sans, Helvetica Neue, Helvetica, Arial, sans-serif; font-size: 12px; line-height: 1.6em; color: #48494B; background-color: #EEE; padding: 0; margin: 0; }
    h1, h2, h3, h4, h5, h6 { padding: 0; margin: 0.5em 0 0.5em 0; font-weight: bold; }
    p { padding: 0; margin: 0 0 .25em 0; }
    a, a:link, a:visited, a:hover, a:active { text-decoration: none; }
    #headline { padding: 24px 12px 18px 12px; background-color: #42B8C5; }
    #headline h1 { color: #F5F5F5; font-size: 2rem; margin: 1rem 0; }
    #headline h2 { color: #9CD6DB; font-size: 1.1rem; font-weight: normal; margin: 1rem 0; }
    table { width: 100%; border-collapse: separate; border-spacing: 0; }
    table td, table th { text-align: left; padding: 10px 0 10px 0; vertical-align: baseline; }
    table th.head {  padding: 5px 0 10px 0; background-color: #FFF; vertical-align: baseline; border-bottom: 2px solid #DBDBDB; }
    table th.head h3 { margin: 3px 0; }
    table td.trace { background-color: #F5F5F5; vertical-align: baseline; }
    table.exception { margin-bottom: 20px; border-top: 2px solid #DBDBDB; }
    table.exception td { border-bottom: 1px solid #DBDBDB; }
    td.stacktrace { padding-top: 0; padding-bottom: 0; background-color: #FFF; }
    td.stacktrace table { border-spacing: 0; }
    table.exception a:link code, table.exception a:visited code { color: #42B8C5; }
    table.exception a:hover code, table.exception a:active code { color: #2F9099; }
    td.stacktrace tr:last-child td { border: none; }
    #main { background-color: #FFF; padding: 2rem; }
    #side { min-width: 240px; padding: 5px 15px; background-color: #E9ECEF; }
    #side h1, #side h2, #side h3, #side h4, #side h5, #side h6 { color: #7A7A7A; font-weight: normal; text-transform: uppercase; margin: 1em 0 0.5em 0; }
    .float-right { float: right; }
    .separator { color: #999; }
    .classname { color: #42B8C5; }
    .namespace { }
    .method { }
    .number { display: inline-block; width: 20px; padding: 1px 6px; margin-right: 10px; text-align: center; background-color: #DBDBDB; border-radius: 5px; }
    .errorclass { font-weight: bold; }
    .errorfile { margin-left: 42px; }
    code { font-family: Consolas, Menlo, Monaco, "Lucida Console", "Liberation Mono", "DejaVu Sans Mono", "Courier New", monospace, serif; font-size: 12px; font-weight: normal; color: #42B8C5; padding: 3px 4px 1px 4px; background-color: #E9ECEF; }
    code.success { color: #48494B; background-color: #9FF781; }
    code.warning { color: #48494B; background-color: #F4FA58; }
    code.error { color: #48494B; background-color: #FA5858; }
    span.success { background-color: #9FF781; padding: 0 1px; }
    span.warning { background-color: #F4FA58; padding: 0 1px; }
    span.error { background-color: #FA5858; padding: 0 1px; }
  </style>
</head>
<body>
HTML;
    }

    /**
     * @return string
     */
    private function renderPageFooter()
    {
        return '</body></html>';
    }

    /**
     * @param array $exception
     *
     * @return string
     */
    private function renderExceptionData($exception)
    {
        $content = '';
        $content .= '<h3>' . $this->renderExceptionHeadline($exception['class']) . '</h3>';
        $content .= '<table class="exception">';
        $content .= '<tr><th class="head">';
        $content .= '<h2>' . htmlspecialchars($exception['message'], ENT_QUOTES) . '</h2>';
        $content .= "<div><a href='editor://open?file={$exception['file']}&line={$exception['line']}'>";
        $content .= "<code>{$exception['file']}:{$exception['line']}</code>";
        $content .= '</div>';
        $content .= '</th></tr>';

        if (!empty($exception['trace'])) {
            $content .= '<tr><td class="stacktrace">';
            $content .= $this->renderStackTrace($exception['trace']);
            $content .= '</td></tr>';
        }
        $content .= '</table>';

        // Render previous exceptions at the end
        if ($exception['previous'] !== null) {
            $content .= $this->renderExceptionData($exception['previous']);
        }

        return $content;
    }

    /**
     * @param string $className Full-qualified class name
     *
     * @return string
     */
    private function renderExceptionHeadline($className)
    {
        $classNameParts = explode('\\', $className);
        $partsSize = count($classNameParts) - 1;

        $headline = '';
        foreach ($classNameParts as $index => $part) {
            if ($index === $partsSize) {
                $headline .= "<span class='classname'>{$part}</span>";
            } else {
                $headline .= "<span class='namespace'>{$part}</span>";
                $headline .= " <small class='separator'>\</small> ";
            }
        }

        return $headline;
    }

    /**
     * @param $stackTrace
     *
     * @return string
     */
    private function renderStackTrace($stackTrace)
    {
        $traceSize = count($stackTrace);

        $content = '<table class="stacktrace">';
        foreach ($stackTrace as $index => $trace) {

            // @todo Einkommentieren wenn ErrorHandler erprobt und stabil
            //if (isset($trace['class']) && $trace['class'] === ErrorHandler::class) {
            //    continue;
            //}

            $number = $traceSize - $index;
            $editorLink = 'editor://open?file=' . urlencode($trace['file']) . '&line=' . urlencode($trace['line']);
            $content .= '<tr>';
            $content .= '<td>';
            $content .= '<div class="errorclass">';
            $content .= "<span class='number'>{$number}</span>";
            $content .= "<span>{$trace['class']}</span>";
            $content .= "<span class='method'><span>&rarr;</span>{$trace['function']}()</span>";
            $content .= '</div>';
            if (!empty($trace['file'])) {
                $content .= "<div class='errorfile'><a href='{$editorLink}'>";
                $content .= "<code>{$trace['file']}:{$trace['line']}</code>";
                $content .= '</a></div>';
            }
            $content .= '</td>';
            $content .= '</tr>';
        }
        $content .= '</table>';

        return $content;
    }

    /**
     * @param array $data
     *
     * @return string
     */
    private function renderInformationData($data)
    {
        $content = "<h3>Systeminformationen</h3>\n";

        $software = $data['software'];
        $content .= "<h4>Software</h4>\n";
        $content .= '<p>Xentral-Revision: <code>';
        $content .= !empty($software['xentral_revision']) ? $software['xentral_revision'] : '--';
        $content .= "</code></p>\n";
        $content .= '<p>Xentral-Version: <code>';
        $content .= !empty($software['xentral_version']) ? $software['xentral_version'] : '--';
        $content .= "</code>\n";
        $content .= '<p>FPDF-Version: <code>';
        $content .= !empty($software['fpdf_version']) ? $software['fpdf_version'] : '--';
        $content .= "</code>\n";

        $general = $data['php']['general'];
        $content .= "<h4>PHP</h4>\n";
        $version = "{$general['version_major']}.{$general['version_minor']}.{$general['version_release']}";
        $content .= "<p>Version: <code>{$version}</code> ({$general['version']})</p>\n";
        $content .= "<p>Server-API: <code>{$general['server_api']}</code></p>\n";
        $content .= "<p>Binary-Pfad: <code>{$general['binary_dir']}</code></p>\n";
        $content .= "<p>php.ini: <code>{$general['php_ini_dir']}</code></p>\n";

        $settings = $data['php']['settings'];
        $content .= "<h4>PHP-Einstellungen:</h4>\n";
        foreach ($settings as $setting) {
            switch ($setting['setting']) {
                case 'max_execution_time':
                    $cssClass = $setting['int_value'] <= 0 || $setting['int_value'] >= 30 ? '' : 'warning';
                    break;
                case 'max_input_time':
                    $cssClass = $setting['int_value'] <= 0 || $setting['int_value'] >= 30 ? '' : 'warning';
                    break;
                case 'post_max_size':
                    $cssClass = $setting['int_value'] >= 8 * 1024 * 1024 ? '' : 'warning';
                    break;
                case 'upload_max_filesize':
                    $cssClass = $setting['int_value'] >= 8 * 1024 * 1024 ? '' : 'warning';
                    break;
                case 'memory_limit':
                    $cssClass = $setting['int_value'] >= 256 * 1024 * 1024 ? '' : 'warning';
                    break;
                default:
                    $cssClass = '';
                    break;
            }
            $content .= sprintf(
                '<p><code class="%s">%s = %s</code></p>' . "\n",
                $cssClass, $setting['setting'], $setting['raw_value']
            );
        }

        $content .= "<h4>PHP-Erweiterungen</h4>\n";

        $defined = $data['php']['extensions']['defined'];
        $content .= '<h5>Benötigt</h5><p>';
        foreach ($defined as $extension => $isAvailable) {
            $failedCssClass = in_array($extension, self::REQUIRED_PHP_EXTENSIONS, true) ? 'error' : 'warning';
            $cssClass = $isAvailable === true ? '' : $failedCssClass;
            $content .= sprintf('<code class="%s">%s</code>', $cssClass, $extension) . ', ';
        }
        $content = substr_replace($content, '', -2);
        $content .= "</p>\n";

        /*$other = $data['php']['extensions']['other'];
        $content .= "<h5>Sonstige</h5><p>";
        foreach ($other as $extension => $available) {
            $content .= sprintf('<code>%s</code>', $extension) . ', ';
        }
        $content = substr_replace($content, '', -2);
        $content .= "</p>\n";*/

        $env = $data['env'];
        $content .= "<h4>Umgebung</h4>\n";
        $content .= "<p>Username: <code>{$env['username']}</code></p>\n";
        $content .= "<p>Home-Directory: <code>{$env['home_dir']}</code></p>\n";
        $content .= "<p>Document-Root: <code>{$env['document_root']}</code></p>\n";
        $content .= "<p>Script-Filename: <code>{$env['script_filename']}</code></p>\n";
        if ($env['script_owner'] !== null) {
            $content .= "<p>Script-Owner/-Group: <code>{$env['script_owner']}:{$env['script_group']}</code></p>\n";
        }

        $server = $data['server'];
        $content .= "<h4>Webserver</h4>\n";
        $content .= '<p>Software: <code>' . (!empty($server['software']) ? $server['software'] : '--') . "</code></p>\n";
        $content .= '<p>Signatur: <code>' . (!empty($server['signature']) ? $server['signature'] : '--') . "</code></p>\n";
        $content .= "<p>Host: <code>{$server['name']}</code> (<code>{$server['addr']}:{$server['port']}</code>)</p>\n";

        $request = $data['request'];
        $content .= "<h4>Request</h4>\n";
        $content .= "<p>Schema: <code>{$request['scheme']}</code></p>\n";
        $content .= "<p>Method/Uri: <code>{$request['method']} " . htmlspecialchars($request['uri']) . "</code></p>\n";
        $content .= '<p>Referer: <code>' . (!empty($request['referer']) ? htmlspecialchars($request['referer']) : '--') . "</code></p>\n";
        $content .= '<p>UserAgent: <code>' . (!empty($request['user_agent']) ? $request['user_agent'] : '--') . "</code></p>\n";
        $content .= '<p>AJAX-Request: <code>' . ($request['is_ajax'] === true ? 'true' : 'false') . "</code></p>\n";
        $content .= '<p>HTTPS-Request: <code>' . ($request['is_https'] === true ? 'true' : 'false') . "</code></p>\n";
        $content .= "<p>Timestamp: <code>{$request['time']}</code></p>\n";

        return $content;
    }
}
