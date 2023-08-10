<?php

declare(strict_types=1);

namespace Xentral\Components\I18n\Iso3166;

/**
 * Names for the iso3166 list.
 *
 * @see      \Xentral\Components\I18n\Iso3166
 * @author   Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 */
abstract class Name/* extends Ruga_Enum */
{
    /** Englisch */
    const ENG = 'NAME_eng';
    /** Französisch */
    const FRA = 'NAME_fra';
    /** Deutsch */
    const DEU = 'NAME_deu';
    
    
    protected static $fullnameMap = [
        self::ENG => 'Englische Bezeichnung',
        self::FRA => 'Französische Bezeichnung',
        self::DEU => 'Deutsche Bezeichnung',
    ];
}
