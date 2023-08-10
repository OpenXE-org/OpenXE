<?php

declare(strict_types=1);

namespace Xentral\Components\I18n;

use Xentral\Components\Database\Database;
use Xentral\Components\Http\Request;
use Xentral\Components\Http\Session\Session;
use Xentral\Core\DependencyInjection\ServiceContainer;

final class Bootstrap
{
    /**
     * @return array
     */
    public static function registerServices(): array
    {
        return [
            'Localization' => 'onInitLocalization',
        ];
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
        
        
        // Get User
        $usersettings = [];
        if ($user = $app->User) {
            // Get User's address from user
            $userAddress = $db->fetchRow(
                $db->select()->cols(['*'])->from('adresse')->where('id=:id'),
                ['id' => $user->GetAdresse()]
            );
            
            
            // Get language from user account and normalize to 3-letter-code and 2-letter-code
            $userSprache = strtolower($user->GetSprache());
            $userLang2 = null;
            $userLang3 = null;
            foreach ((new Iso639(new Iso639\Filter\CentralEurope())) as $key => $val) {
                if (array_filter($val, function ($str) use ($userSprache) {
                    return $str && (strtolower($str) == $userSprache);
                })) {
                    $userLang2 = $val[Iso639\Key::ALPHA_2];
                    $userLang3 = $val[Iso639\Key::ALPHA_3];
                }
            }
            if ($userLang3) {
                $usersettings['language'] = $userLang3;
            }
            
            
            // Get region from user account and normalize to 2-letter-code
            $userLand = strtolower($userAddress['land'] ?? '');
            $userRegion = null;
            foreach ((new Iso3166(new Iso3166\Filter\CentralEurope())) as $key => $val) {
                if (array_filter($val, function ($str) use ($userLand) {
                    return $str && (strtolower($str) == $userLand);
                })) {
                    $userRegion = $val[Iso3166\Key::ALPHA_2];
                }
            }
            if ($userLang2 && $userRegion) {
                $usersettings['locale'] = "{$userLang2}_{$userRegion}";
            }
        }
        
        // Create Localization object
        return new Localization($request, $session, $usersettings);
    }
}
