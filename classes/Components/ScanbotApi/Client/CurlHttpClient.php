<?php

namespace Xentral\Components\ScanbotApi\Client;

class CurlHttpClient
{
    protected $url;
    protected $method;
    protected $header;
    protected $post;

    // Nachfolgende Properties sind erst nach dem Absenden gefüllt
    protected $errorCode;
    protected $errorMessage;
    protected $responseContent;
    protected $responseDebugInfo;
    protected $responseStatusCode;
    protected $responseContentType;

    protected $isSent = false;
    protected $hasError = false;

    /**
     * @param string     $method [GET|POST|PUT]
     * @param string     $url
     * @param array      $header HTTP-Header
     * @param array|null $post   Nutzdaten für POST-/PUT-Requests (GET-Parameter in URL übergeben)
     */
    public function __construct($method, $url, array $header = [], $post = null)
    {
        $this->url = $url;
        $this->method = strtoupper($method);
        $this->header = $header;
        $this->post = $post;
    }

    /**
     * @return string
     */
    public function GetContent()
    {
        if (!$this->IsSent()) {
            $this->Send();
        }

        return $this->responseContent;
    }

    /**
     * @return bool
     */
    protected function IsSent()
    {
        return $this->isSent;
    }

    /**
     * Request abschicken
     *
     * @return void
     */
    protected function Send()
    {
        $this->isSent = true;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url);
        if ($this->method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $this->post);
        }
        if ($this->method === 'PUT') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
            curl_setopt($ch, CURLOPT_POSTFIELDS, $this->post);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->header);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_VERBOSE, false);

        $this->responseContent = curl_exec($ch);
        $this->responseStatusCode = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        $this->responseContentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        $this->responseDebugInfo = curl_getinfo($ch);

        if ($this->responseContent === false) {
            $this->hasError = true;
            $this->errorCode = curl_errno($ch);
            $this->errorMessage = curl_error($ch);
        }

        curl_close($ch);
    }

    /**
     * @return int
     */
    public function GetStatusCode()
    {
        if (!$this->IsSent()) {
            $this->Send();
        }

        return (int)$this->responseStatusCode;
    }

    /**
     * @return array
     */
    public function GetDebugInfo()
    {
        if (!$this->IsSent()) {
            $this->Send();
        }

        return $this->responseDebugInfo;
    }

    /**
     * @return bool
     */
    public function HasError()
    {
        if (!$this->IsSent()) {
            $this->Send();
        }

        return $this->hasError;
    }

    /**
     * @return int
     */
    public function GetErrorCode()
    {
        if (!$this->IsSent()) {
            $this->Send();
        }

        return $this->errorCode;
    }

    /**
     * @return string
     */
    public function GetErrorMessage()
    {
        if (!$this->IsSent()) {
            $this->Send();
        }

        return $this->errorMessage;
    }
}

