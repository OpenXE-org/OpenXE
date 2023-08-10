<?php

declare(strict_types=1);

namespace Xentral\Components\I18n;

use Locale;
use Xentral\Components\Http\Request;
use Xentral\Components\Http\Session\Session;
use Xentral\Components\I18n\Exception\LanguageNotInitializedException;
use Xentral\Components\I18n\Exception\UnsupportedLocaleStringException;

final class Localization implements LocalizationInterface
{
    private array $config;
    
    private ?Request $request;
    
    private ?Session $session;
    
    private array $usersettings = [];
    
    private array $language = [];
    
    private array $locale = [];
    
    
    
    public function __construct(?Request $request, ?Session $session, array $usersettings = [])
    {
        $this->request = $request;
        $this->session = $session;
        $this->usersettings = $usersettings;
        $this->config = [];
        $this->process();
    }
    
    
    
    public function process()
    {
        // Hardcoded defaults if config is not available
        $localeDefault = $this->config[Localization::LOCALE_DEFAULT] ?? 'de_DE';
        $localeAttrName = $this->config[Localization::LOCALE_ATTRIBUTE_NAME] ?? 'locale';
        $langDefault = $this->config[Localization::LANGUAGE_DEFAULT] ?? 'deu';
        $langAttrName = $this->config[Localization::LANGUAGE_ATTRIBUTE_NAME] ?? 'language';
        
        $segmentName = 'i18n';
//        $session = $this->session;
//        $request = $this->request;
        
        // Get the locale from the session, if available
        if ($this->session && ($locale = $this->session->getValue($segmentName, $localeAttrName))) {
        } else {
            // Get locale from request, fallback to the user's browser preference
            if ($this->request) {
                $locale = $this->request->attributes->get(
                    $localeAttrName,
                    Locale::acceptFromHttp(
                        $this->request->getHeader('Accept-Language', $localeDefault)
                    ) ?? $localeDefault
                );
            } else {
                $locale = Locale::acceptFromHttp($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? $localeDefault);
            }
        }
        // Get locale from user
        // This overrides all previous attempts to find a locale
        if (array_key_exists('locale', $this->usersettings)) {
            $locale = $this->usersettings['locale'];
        }
        // Get locale from query string
        // This overrides all previous attempts to find a locale
        if ($this->request) {
            $locale = $this->request->getParam($localeAttrName, $locale ?? $localeDefault);
        } else {
            $locale = $_GET[$localeAttrName] ?? $locale ?? $localeDefault;
        }
        
        
        // Get the language from the session, if available
        if ($this->session && ($language = $this->session->getValue($segmentName, $langAttrName))) {
        } else {
            // Get language from request, fallback to the current locale
            if ($this->request) {
                $language = $this->request->attributes->get($langAttrName, Locale::getPrimaryLanguage($locale));
            } else {
                $language = Locale::getPrimaryLanguage($locale);
            }
        }
        // Get language from user
        // This overrides all previous attempts to find a language
        if (array_key_exists('language', $this->usersettings)) {
            $language = $this->usersettings['language'];
        }
        // Get language from query string
        // This overrides all previous attempts to find a language
        if ($this->request) {
            $language = $this->request->getParam($langAttrName, $language ?? $langDefault);
        } else {
            $language = $language ?? $langDefault;
        }
        
        // Check language against the data from Iso639 (and normalize to 3-letter-code)
        $language = (new Iso639())->find($language, Iso639\Key::DEFAULT, $langDefault);
        
        
        // Store the locale and language to the LocalizationInterface
        $this->setLanguage($language);
        $this->setLocale($locale);
        
        // Store the locale and language to the session
        if ($this->session) {
            $this->session->setValue($segmentName, $localeAttrName, $locale);
            $this->session->setValue($segmentName, $langAttrName, $language);
        }
        
        // Store the locale and language as a request attribute
        if ($this->request) {
            $this->request->attributes->set($localeAttrName, $locale);
            $this->request->attributes->set($langAttrName, $language);
        }
        
        // Set the default locale
        Locale::setDefault($locale);
        
//        error_log(self::class . ": {$locale}");
    }
    
    
    
    /**
     * Set the language.
     *
     * @param string $language
     */
    public function setLanguage(string $language)
    {
        $this->language[Iso639\Key::DEFAULT] = (new \Xentral\Components\I18n\Iso639())->find(
            $language,
            Iso639\Key::DEFAULT
        );
    }
    
    
    
    /**
     * Return the language string as defined by $key.
     *
     * @param string|null $key A constant from Iso639\Key
     *
     * @return string
     */
    public function getLanguage(string $key = null): string
    {
        if (!$key) {
            $key = Iso639\Key::DEFAULT;
        }
        if (!($this->language[$key] ?? null)) {
            if (!($this->language[Iso639\Key::DEFAULT] ?? null)) {
                throw new LanguageNotInitializedException("Language is not set for key '" . Iso639\Key::DEFAULT . "'");
            }
            $this->language[$key] = (new \Xentral\Components\I18n\Iso639())->find(
                $this->language[Iso639\Key::DEFAULT],
                $key
            );
        }
        return $this->language[$key];
    }
    
    
    
    /**
     * Set the locale.
     *
     * @param string $locale
     */
    public function setLocale(string $locale)
    {
        $parsedLocale = Locale::parseLocale($locale);
        $locale = Locale::composeLocale([
                                            'language' => $parsedLocale['language'],
                                            'region' => $parsedLocale['region'],
                                        ]);
        
        if(!$locale) throw new UnsupportedLocaleStringException("The given locale string '{$locale}' is not supported");
        
        $this->locale[Iso3166\Key::DEFAULT] = $locale;
    }
    
    
    
    /**
     * Return the locale string as defined by $key.
     *
     * @param string|null $key A constant from Iso3166\Key
     *
     * @return string
     */
    public function getLocale(string $key = null): string
    {
        return $this->locale[Iso3166\Key::DEFAULT];
    }
    
    
}