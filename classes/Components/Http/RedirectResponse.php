<?php

namespace Xentral\Components\Http;

class RedirectResponse extends Response
{
    /**
     * @param string $url Absolute or relative url
     *
     * @return self
     */
    public static function createFromUrl($url)
    {
        $content = self::getRedirectTemplate($url);

        return new self(
            $content,
            Response::HTTP_MOVED_TEMPORARILY,
            [
                'Length'   => (string)strlen($content),
                'Location' => $url,
            ]
        );
    }

    /**
     * @param string $url
     *
     * @return string
     */
    private static function getRedirectTemplate($url)
    {
        $template = <<<'HTML'
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html lang="en" xml:lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <meta http-equiv="refresh" content="0;URL='%1$s'" />
  <title>Redirecting</title>
</head>
<body>
  <h1>Redirecting...</h1>
  <p>You are being redirected. If nothing happens, please <a href="%1$s">follow this link</a>.</p>
</body>
</html> 
HTML;

        return sprintf($template, $url);
    }
}
