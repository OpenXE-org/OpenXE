<?php


namespace Xentral\Widgets\ClickByClickAssistant;


class Bootstrap
{
    /**
     * @return array
     */
    public static function registerJavascript()
    {
        return [
            'ClickByClickAssistant' => [
                './classes/Widgets/ClickByClickAssistant/www/js/click_by_click_assistant.js',
            ],
        ];
    }

    /**
     * @return array
     */
    public static function registerStylesheets()
    {
        return [
            'ClickByClickAssistant' => [
                './classes/Widgets/ClickByClickAssistant/www/css/click_by_click_assistant.css',
            ],
        ];
    }
}
