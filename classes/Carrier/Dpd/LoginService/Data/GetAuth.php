<?php

// SPDX-FileCopyrightText: 2025 Andreas Palm
//
// SPDX-License-Identifier: AGPL-3.0-only

namespace Xentral\Carrier\Dpd\LoginService\Data;

class GetAuth
{
    public string $delisId = '';
    public string $password = '';
    public string $messageLanguage = 'de_DE';
}