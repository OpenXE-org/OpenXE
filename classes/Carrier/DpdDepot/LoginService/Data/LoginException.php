<?php

// SPDX-FileCopyrightText: 2025 Andreas Palm
//
// SPDX-License-Identifier: AGPL-3.0-only

namespace Xentral\Carrier\DpdDepot\LoginService\Data;

class LoginException
{
    public ?string $additionalData = null;
    public ?string $additionalInfo = null;
    public ?string $errorClass = null;
    public ?string $errorCode = null;
    public ?string $fullMessage = null;
    public ?string $language = null;
    public ?string $message = null;
    public ?string $shortMessage = null;
    public ?string $systemFullMessage = null;
    public ?string $systemMessage = null;
    public ?string $systemShortMessage = null;
}