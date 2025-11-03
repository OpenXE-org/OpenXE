<?php

// SPDX-FileCopyrightText: 2025 Andreas Palm
//
// SPDX-License-Identifier: AGPL-3.0-only

namespace Xentral\Carrier\DpdDepot\Data;

class Notification
{
    public NotificationChannel $channel = NotificationChannel::Email;
    public string $value = '';
}