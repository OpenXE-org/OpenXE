<?php
/**
 * SPDX-FileCopyrightText: 2023 Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 * SPDX-License-Identifier: AGPL-3.0-only
 */

declare(strict_types=1);

namespace Xentral\Components\I18n\Iso639;


/**
 * Keys for the iso639 list.
 *
 * @see      \Xentral\Components\I18n\Iso639
 * @author   Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 */
abstract class Key/* extends Ruga_Enum*/
{
    /** Key: Alpha-2 code */
    const ALPHA_2 = '639-1';
    const ISO639_1 = '639-1';
    
    /** Key: Alpha-3 code */
    const ALPHA_3 = '639-2';
    const ISO639_2 = '639-2';
    
    /** Key: Top Level Domain */
    const ONELETTER = '1L';
    
    /** Key: Name */
    const NAME_eng = 'NAME_eng';
    const NAME_fra = 'NAME_fra';
    const NAME_deu = 'NAME_deu';
    
    
    const DEFAULT = self::ALPHA_3;
    
    
    protected static $fullnameMap = [
        self::ALPHA_2 => 'ISO 639 Alpha-2',
        self::ALPHA_3 => 'ISO 639 Alpha-3',
        self::ONELETTER => 'ISO 639 Alpha-1',
        self::NAME_eng => 'Englische Bezeichnung',
        self::NAME_fra => 'Französische Bezeichnung',
        self::NAME_deu => 'Deutsche Bezeichnung',
    ];
    
    
}

