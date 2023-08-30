<?php
/*
 * SPDX-FileCopyrightText: 2023 Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 * SPDX-License-Identifier: AGPL-3.0-only
 */

declare(strict_types=1);

namespace Xentral\Components\I18n;

use Locale;
use Xentral\Components\Http\Request;
use Xentral\Components\Http\Session\Session;
use Xentral\Components\I18n\Exception\LanguageNotInitializedException;
use Xentral\Components\I18n\Exception\LocaleNotSetException;
use Xentral\Components\I18n\Exception\UnsupportedLocaleStringException;

/**
 * Provides a central service for localization.
 *
 * @author   Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 */
final class Localization implements LocalizationInterface
{
    private array $config;
    
    private ?Request $request;
    
    private ?Session $session;
    
    private array $usersettings = [];
    
    private array $language = [];
    
    private string|null $locale;
    
    
    
    public function __construct(?Request $request, ?Session $session, array $usersettings = [], array $config = [])
    {
        $this->request = $request;
        $this->session = $session;
        $this->usersettings = $usersettings;
        $this->config = $config;
        
        // Set config if no default is given
        $this->config[Localization::LOCALE_DEFAULT]=$this->config[Localization::LOCALE_DEFAULT] ?? 'de_DE';
        $this->config[Localization::LANGUAGE_DEFAULT]=$this->config[Localization::LANGUAGE_DEFAULT] ?? 'deu';
        
        $this->process();
    }
    
    
    
    public function process()
    {
        // Hardcoded defaults if config is not available
//        $localeDefault = $this->config[Localization::LOCALE_DEFAULT] ?? 'de_DE';
        $localeAttrName = $this->config[Localization::LOCALE_ATTRIBUTE_NAME] ?? 'locale';
        $langDefault = $this->config[Localization::LANGUAGE_DEFAULT] ?? 'deu';
        $langAttrName = $this->config[Localization::LANGUAGE_ATTRIBUTE_NAME] ?? 'language';
        
        $segmentName = 'i18n';
        
        $this->setLocale((string)$this->config[Localization::LOCALE_DEFAULT]);
        
        // Get the locale from the session, if available
        if ($this->session && ($sessionLocale = $this->session->getValue($segmentName, $localeAttrName))) {
            $this->setLocale((string)$sessionLocale, $this->getLocale());
        } else {
            // Get locale from request, fallback to the user's browser preference
            if ($this->request) {
                try {
                    $this->setLocale((string)$this->request->attributes->get($localeAttrName, ''));
                } catch (UnsupportedLocaleStringException $e) {
                    $this->setLocale(
                        (string)
                        Locale::acceptFromHttp(
                            $this->request->getHeader('Accept-Language', '')
                        )
                        ,
                        $this->getLocale()
                    );
                }
            } else {
                $this->setLocale((string)Locale::acceptFromHttp($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? ''), $this->getLocale());
            }
        }
        // Get locale from user
        // This overrides all previous attempts to find a locale
        if (array_key_exists('locale', $this->usersettings)) {
            $this->setLocale((string)$this->usersettings['locale'], $this->getLocale());
        }
        // Get locale from query string
        // This overrides all previous attempts to find a locale
        if ($this->request) {
            $this->setLocale((string)$this->request->getParam($localeAttrName), $this->getLocale());
        } else {
            $this->setLocale((string)$_GET[$localeAttrName], $this->getLocale());
        }
        
        
        // Get the language from the session, if available
        if ($this->session && ($language = $this->session->getValue($segmentName, $langAttrName))) {
        } else {
            // Get language from request, fallback to the current locale
            if ($this->request) {
                $language = $this->request->attributes->get($langAttrName, Locale::getPrimaryLanguage($this->getLocale()));
            } else {
                $language = Locale::getPrimaryLanguage($this->getLocale());
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
        
        // Store the locale and language to the session
        if ($this->session) {
            $this->session->setValue($segmentName, $localeAttrName, $this->getLocale());
            $this->session->setValue($segmentName, $langAttrName, $this->getLanguage());
        }
        
        // Store the locale and language as a request attribute
        if ($this->request) {
            $this->request->attributes->set($localeAttrName, $this->getLocale());
            $this->request->attributes->set($langAttrName, $this->getLanguage());
        }
        
        // Set the default locale
        Locale::setDefault($this->getLocale());
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
     * @param string      $locale
     * @param string|null $fallbackLocale
     */
    public function setLocale(string $locale, string|null $fallbackLocale = null): void
    {
        if(!empty($locale)) {
            // Check if locale is already set
            try {
                if ($locale == $this->getLocale()) {
                    return;
                }
            } catch (LocaleNotSetException $e) {
            };
            
            // Parse and re-compose locale to make sure, it is sane
            $parsedLocale = Locale::parseLocale($locale);
            $composedLocale = Locale::composeLocale([
                                                        'language' => $parsedLocale['language'],
                                                        'region' => $parsedLocale['region'],
                                                    ]);
        } else {
            $composedLocale=null;
        }
        
        // If sanity check fails, set fallbackLocale if present
        // throw exception otherwise
        if (!$composedLocale) {
            if ($fallbackLocale === null) {
                throw new UnsupportedLocaleStringException("The given locale string '{$locale}' is not supported");
            } else {
                $this->setLocale($fallbackLocale);
                return;
            }
        }
        
        $this->locale = $composedLocale;
    }
    
    
    
    /**
     * Return the locale string as defined by $key.
     *
     * @return string
     */
    public function getLocale(): string
    {
        if (!isset($this->locale)) {
            throw new LocaleNotSetException();
        }
        return $this->locale;
    }
    
    
    
    /**
     * Return a new localization object using the given adresse array as source for language and region.
     *
     * @param array $adresse
     *
     * @return $this
     */
    public function withAdresse(array $adresse): self
    {
        $localization = clone $this;
        
        // Find language from address array or keep current language
        if (!$lang = Bootstrap::findLanguage($adresse['sprache'] ?? '')) {
            $lang = Bootstrap::findLanguage($this->getLanguage());
        }
        if ($lang) {
            $localization->setLanguage($lang[Iso639\Key::ALPHA_3]);
        }
        
        // Find region from address or keep current region
        if (!$region = Bootstrap::findRegion($adresse['land'] ?? '')) {
            $parsedLocale = Locale::parseLocale($this->getLocale());
            $region = Bootstrap::findRegion($parsedLocale['region']);
        }
        if ($lang && $region) {
            $localization->setLocale("{$lang[Iso639\Key::ALPHA_2]}_{$region[Iso3166\Key::ALPHA_2]}");
        }
        
        return $localization;
    }
    
}