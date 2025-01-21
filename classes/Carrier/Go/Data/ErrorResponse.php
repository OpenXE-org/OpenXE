<?php

// SPDX-FileCopyrightText: 2025 Andreas Palm
//
// SPDX-License-Identifier: AGPL-3.0-only

namespace Xentral\Carrier\Go\Data;

class ErrorResponse {
    public string $message;
    public int $errorCode;
    public int $status;
    public \DateTime $timeStamp;
}