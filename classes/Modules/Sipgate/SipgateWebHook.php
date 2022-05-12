<?php

namespace Xentral\Modules\Sipgate;

use DOMAttr;
use \DOMDocument;
use \DOMElement;

/**
 * Class SipGateWebHook
 *
 * Manage incoming requests from sipgate web hook api. This class is only used in
 * www/pages/callcenter.php in Callcenter::CallcenterCall in switch 'sipgate' to
 * create the xml response.
 *
 * It also provides methods to hang up the call etc.
 *
 * @see     https://developer.sipgate.io/push-api/api-reference
 * @see     https://github.com/sipgate/sipgate.io/blob/master/examples/php/
 *
 * @example new SipGateWebHook($_POST, 'http://localhost:8080');
 */
class SipgateWebHook
{
    /** @var array $data The call date. By default $_POST */
    private $data = [];

    /** @var string $url Optional the callback url to listen for following events. */
    private $url = '';

    /**
     * @param array  $data The call data
     * @param string The url used as web hook for 'onAnswer' & 'onHangup' events.
     */
    public function __construct($data, $url = '')
    {
        $data = (array)$data;
        $data['timestamp'] = time();
        $data['date'] = date('Y-m-d H:i:s');
        $this->data = $data;

        if (is_string($url) && filter_var($url, FILTER_VALIDATE_URL)) {
            $this->url = $url;
        }
    }

    /**
     * @param string $key
     * @param string $fallback
     *
     * @return mixed|string
     */
    public function getData($key, $fallback = '')
    {
        return array_key_exists($key, $this->data)
            ? $this->data[$key]
            : $fallback;
    }

    /**
     * Redirect the call and alter your caller id (call charges apply).
     * Calls with direction=in can be redirected to up to 5 targets.
     *
     * @return void
     */
    public function dial()
    {
        $config = [
            // 'voicemail' => true,
            'suppress' => true,
            'numbers'  => [
                123,
                456,
                678,
            ],
        ];

        /**
         * @param DOMDocument $dom
         * @param DOMElement  $parent
         *
         * @return DOMElement|null
         */
        $callback = static function ($dom, $parent) use ($config) {

            if (isset($config['voicemail']) && $config['voicemail']) {
                return $dom->createElement('Voicemail');
            }

            /*
             * Suppress phone number
             */
            if ((isset($config['suppress']) && $config['suppress']) ||
                (isset($config['anonymous']) && $config['anonymous'])) {

                $anonymous = $dom->createAttribute('anonymous');
                $anonymous->value = 'true';

                $parent->appendChild($anonymous);
            }

            if (isset($config['number']) && $config['number']) {
                /*
                 * override 'numbers' with 'number'
                 */
                $config['numbers'] = $config['number'];
            }

            /*
             * Redirect incoming call to (multiple) destination(s)
             * Calls with direction=in can be redirected to up to 5 targets.
             */
            if (isset($config['numbers']) && $config['numbers']) {
                $numbers = (array)$config['numbers'];
                $numbers = array_filter($numbers);
                $numbers = array_slice($numbers, 0, 5);

                foreach ($numbers as $number) {
                    $numberElement = $dom->createElement('Number', $number);
                    $parent->appendChild($numberElement);
                }
            }

            return null;
        };

        $this->createXMLResponse('Dial', $callback);
    }

    /**
     * Send call to voice mail
     *
     * <?xml version="1.0" encoding="UTF-8"?>
     * <Response>
     *  <Dial>
     *    <Voicemail />
     *  </Dial>
     * </Response>
     *
     * @return void
     */
    public function voiceMail()
    {
        /**
         * @param DOMDocument $dom
         *
         * @return DOMElement|null
         */
        $callback = static function ($dom) {
            return $dom->createElement('Voicemail');
        };

        $this->createXMLResponse('Dial', $callback);
    }

    /**
     * Reject call signaling busy
     *
     * <?xml version="1.0" encoding="UTF-8"?>
     * <Response>
     *  <Reject reason="busy" />
     * </Response>
     *
     * @return void
     */
    public function busy()
    {
        /**
         * @param DOMDocument $dom
         *
         * @return DOMAttr
         */
        $callback = static function ($dom) {
            $hangupReason = $dom->createAttribute('reason');
            $hangupReason->value = 'busy';

            return $hangupReason;
        };

        $this->createXMLResponse('Reject', $callback);
    }

    /**
     * Reject call
     *
     * <?xml version="1.0" encoding="UTF-8"?>
     * <Response>
     *  <Reject />
     * </Response>
     *
     * @return void
     */
    public function reject()
    {
        $this->createXMLResponse('Reject');
    }

    /**
     * Hang up calls
     *
     * <?xml version="1.0" encoding="UTF-8"?>
     * <Response>
     *  <Hangup />
     * </Response>
     *
     * @return void
     */
    public function hangUp()
    {
        $this->createXMLResponse('Hangup');
    }

    /**
     * Play a sound file
     *
     * @see: https://developer.sipgate.io/push-api/api-reference/#play
     *
     * Please note:
     * Currently the sound file needs to be a mono 16bit PCM WAV file with a sampling rate of 8kHz.
     * You can use conversion tools like the open source audio editor Audacity to convert any sound
     * file to the correct format. Linux users might want to use mpg123 to convert the file:
     * $ mpg123 --rate 8000 --mono -w output.wav input.mp3
     *
     * <?xml version="1.0" encoding="UTF-8"?>
     * <Response>
     *  <Play>
     *    <Url>http://example.com/example.wav</Url>
     *  </Play>
     * </Response>
     *
     * @param string $url
     *
     * @return void
     */
    public function play($url)
    {
        /**
         * @param DOMDocument $dom
         *
         * @return DOMElement
         */
        $callback = static function ($dom) use ($url) {
            return $dom->createElement('Url', $url);
        };

        $this->createXMLResponse('Play', $callback);
    }

    /**
     * sets header to xml and displays output
     *
     * @return bool
     */
    public function listenOnFollowingEvents()
    {
        if (headers_sent()) {
            return false;
        }

        if (!$this->url) {
            return false;
        }

        // createXMLResponse starts sending headers & display some content
        $this->createXMLResponse(false);

        return true;
    }

    /**
     * Create XML like:
     *
     * <?xml version="1.0" encoding="UTF-8"?>
     * <Response onAnswer="http://localhost" onHangup="http://localhost">
     * <Reject reason="busy"/>
     * </Response>
     *
     * @param string   $childName
     * @param callable $callback
     *
     * @return void
     */
    private function createXMLResponse($childName, $callback = null)
    {
        $dom = new DOMDocument('1.0', 'UTF-8');
        $response = $dom->createElement('Response');

        /*
         * On new call, set the onAnswer & onHangup flags
         */
        if ($this->url && $this->data['event'] && $this->data['event'] === 'newCall') {
            $url = $this->url;

            /*
             * If you set the onAnswer attribute sipgate.io will push an answer-event,
             * when a call is answered by the other party.
             */
            $response->setAttribute('onAnswer', $url);

            /*
             * If you set the onHangup attribute sipgate.io will push a hangup-event
             * when the call ends.
             */
            $response->setAttribute('onHangup', $url);
        }

        if (is_string($childName) && $childName) {
            /*
           * create the child defined in the $childName argument
           */
            $child = $dom->createElement($childName);

            /*
             * If a callback is given, let's append it's response
             * to the child.
             */
            if ($callback !== null && is_callable($callback)) {
                $element = $callback($dom, $child);
                if ($element) {
                    $child->appendChild($element);
                }
            }
            $response->appendChild($child);
        }
        $dom->appendChild($response);

        header('Content-type: application/xml');
        echo $dom->saveXML();
    }
}
