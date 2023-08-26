<?php
/*
 * SPDX-FileCopyrightText: 2023 Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 * SPDX-License-Identifier: AGPL-3.0-only
 */

declare(strict_types=1);

namespace Xentral\Components\I18n;

use Xentral\Components\Database\Database;
use Xentral\Components\Http\Request;
use Xentral\Components\Http\Session\Session;
use Xentral\Core\DependencyInjection\ServiceContainer;

/**
 * Factory for localization object.
 *
 * @see      Localization
 * @author   Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 */
final class Bootstrap
{
    /**
     * @return array
     */
    public static function registerServices(): array
    {
        return [
            'Localization' => 'onInitLocalization',
            'FormatterService' => 'onInitFormatterService',
        ];
    }
    
    
    
    /**
     * Replaces umlauts with their 2 character representation.
     *
     * @param string $string
     *
     * @return array|string|string[]
     */
    public static function replaceUmlauts(string $string)
    {
        $search = ['ä', 'ö', 'ü', 'Ä', 'Ö', 'Ü', 'ß'];
        $replace = ['ae', 'oe', 'ue', 'Ae', 'Oe', 'Ue', 'ss'];
        return str_replace($search, $replace, $string);
    }
    
    
    
    /**
     * Find the language information from the given string.
     *
     * @param string $lang
     *
     * @return array|null
     */
    public static function findLanguage(string $lang): ?array
    {
        $subject = strtolower($lang);
        foreach ((new Iso639()) as $key => $val) {
            if (array_filter($val, function ($str) use ($subject) {
                return $str && ((strtolower($str) == $subject) || (strtolower(self::replaceUmlauts($str)) == $subject));
            })) {
                return $val;
            }
        }
        return null;
    }
    
    
    
    /**
     * Find the region information from the given string.
     *
     * @param string $region
     *
     * @return array|null
     */
    public static function findRegion(string $region): ?array
    {
        $subject = strtolower($region);
        foreach ((new Iso3166()) as $key => $val) {
            if (array_filter($val, function ($str) use ($subject) {
                return $str && ((strtolower($str) == $subject) || (strtolower(self::replaceUmlauts($str)) == $subject));
            })) {
                return $val;
            }
        }
        return null;
    }
    
    
    
    /**
     * This is the factory for the Localization object.
     *
     * @param ServiceContainer $container
     *
     * @return Localization
     */
    public static function onInitLocalization(ServiceContainer $container): Localization
    {
        /** @var Request $request */
        $request = $container->get('Request');
        /** @var Session $session */
        $session = $container->get('Session');
        /** @var \erpooSystem $app */
        $app = $container->get('LegacyApplication');
        /** @var Database $db */
        $db = $container->get('Database');
        
        $config = [];
        $firmaLang = null;
        $firmaRegion = null;
        // Get language from system settings and normalize to 3-letter-code and 2-letter-code
        if ($firmaLang = self::findLanguage(strval($app->erp->Firmendaten('preferredLanguage')))) {
            $config[Localization::LANGUAGE_DEFAULT] = $firmaLang[Iso639\Key::ALPHA_3];
        }
        
        // Get region from system settings and normalize to 2-letter-code
        if ($firmaLang && ($firmaRegion = self::findRegion(strval($app->erp->Firmendaten('land'))))) {
            $config[Localization::LOCALE_DEFAULT] = "{$firmaLang[Iso639\Key::ALPHA_2]}_{$firmaRegion[Iso3166\Key::ALPHA_2]}";
        }
        
        
        // Get User
        $usersettings = [];
        if ($user = $app->User) {
            // Get User's address from user
            $userAddress = $db->fetchRow(
                $db->select()->cols(['*'])->from('adresse')->where('id=:id'),
                ['id' => $user->GetAdresse()]
            );
            
            // Get language from user account and normalize to 3-letter-code and 2-letter-code
            if ($userLang = self::findLanguage(strval($user->GetSprache()))) {
                $usersettings['language'] = $userLang[Iso639\Key::ALPHA_3];
            }
            
            // Get region from user account and normalize to 2-letter-code
            if ($userLang && ($userRegion = self::findRegion(strval($userAddress['land'])))) {
                $usersettings['locale'] = "{$userLang[Iso639\Key::ALPHA_2]}_{$userRegion[Iso3166\Key::ALPHA_2]}";
            }
        }
        
        // Create Localization object
        return new Localization($request, $session, $usersettings, $config);
    }
    
    
    
    public static function onInitFormatterService(ServiceContainer $container): FormatterService
    {
        $localization = $container->get('Localization');
        $locale = $localization->getLocale();
        return new FormatterService($locale);
    }
    
}
