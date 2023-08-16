<?php
/**
 * SPDX-FileCopyrightText: 2023 Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 * SPDX-License-Identifier: AGPL-3.0-only
 */

declare(strict_types=1);

namespace Xentral\Components\I18n\Iso3166;


/**
 * Keys for the iso3166 list.
 *
 * @see      \Xentral\Components\I18n\Iso3166
 * @author   Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 */
abstract class Key/* extends Ruga_Enum*/
{
    /** Key: Alpha-2 code */
    const ALPHA_2 = 'A2';
    
    /** Key: Alpha-3 code */
    const ALPHA_3 = 'A3';
    
    /** Key: Numeric code */
    const NUMERIC = 'NUM';
    
    /** Key: Top Level Domain */
    const TLD = 'TLD';
    
    /** Key: Currency Code */
    const CURRENCY_CODE = 'CURRENCY_CODE';
    const TELEPHONE_CODE = 'TEL_CODE';
    const REGION = 'REGION';
    const REGION_CODE = 'REGION_CODE';
    const SUBREGION = 'SUBREGION';
    const SUBREGION_CODE = 'SUBREGION_CODE';
    const INTERMEDIATEREGION = 'INTERMEDIATEREGION';
    const INTERMEDIATEREGION_CODE = 'INTERMEDIATEREGION_CODE';
    const NAME_eng = 'NAME_eng';
    const NAME_fra = 'NAME_fra';
    const NAME_deu = 'NAME_deu';
    
    
    /** Key: Postal country name */
    const POST = 'POST';
    
    
    const DEFAULT = self::ALPHA_3;
    
    
    protected static $fullnameMap = [
        self::ALPHA_2 => 'ISO 3166 Alpha-2',
        self::ALPHA_3 => 'ISO 3166 Alpha-3',
        self::NUMERIC => 'ISO 3166 Numerisch',
        self::TLD => 'Top Level Domain',
        self::CURRENCY_CODE => 'Währung',
        self::TELEPHONE_CODE => 'Landesvorwahl',
        self::REGION => 'Region',
        self::REGION_CODE => 'Region Code',
        self::SUBREGION => 'Unter-Region',
        self::SUBREGION_CODE => 'Unter-Region Code',
        self::INTERMEDIATEREGION => 'Intermediate-Region',
        self::INTERMEDIATEREGION_CODE => 'Intermediate-Region Code',
        self::NAME_eng => 'Englische Bezeichnung',
        self::NAME_fra => 'Französische Bezeichnung',
        self::NAME_deu => 'Deutsche Bezeichnung',
        
        self::POST => 'Landesbezeichung Postadresse',
    ];
    
    
}

