<?php
/**
 * SPDX-FileCopyrightText: 2023 Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 * SPDX-License-Identifier: AGPL-3.0-only
 */

declare(strict_types=1);

namespace Xentral\Components\I18n\Iso639\Filter;

use Xentral\Components\I18n\Dataaccess\DataFilterInterface;
use Xentral\Components\I18n\Iso639\Key;


/**
 * Applies a filter to only select central european countries.
 *
 * @see      Custom
 * @see      \Xentral\Components\I18n\Dataaccess\DataFilter
 * @see      \Xentral\Components\I18n\Dataaccess\DataFilterInterface
 * @author   Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 */
class CentralEurope extends Custom implements DataFilterInterface
{
    /**
     * Countries in Europe.
     *
     * @var array
     */
    const CentralEurope_Languages = ['deu', 'fra', 'ita', 'roh', 'spa', 'por', 'eng'];
    
    
    
    /**
     * Set predefined values.
     */
    public function __construct()
    {
        parent::__construct(static::CentralEurope_Languages, Key::ALPHA_3);
    }
}
