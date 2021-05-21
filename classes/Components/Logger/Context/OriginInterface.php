<?php

declare(strict_types=1);

namespace Xentral\Components\Logger\Context;

interface OriginInterface
{
    /** @var string FRONTEND */
    public const TYPE_FRONTEND = 'frontend_request';

    /** @var string LEGACY_API */
    public const TYPE_LEGACY_API = 'legacy_api';

    /** @var string TYPE_REST_API */
    public const TYPE_REST_API = 'rest_api';

    /** @var string TYPE_CALDAV */
    public const TYPE_CALDAV = 'caldav_request';

    /** @var string TYPE_WEBDAV */
    public const TYPE_WEBDAV = 'webdav_request';

    /** @var string TYPE_DEVICE */
    public const TYPE_DEVICE = 'device_request';

    /** @var string TYPE_SCHEDULER_JOB */
    public const TYPE_SCHEDULER_JOB = 'scheduler_job';

    /** @var string TYPE_CLI */
    public const TYPE_CLI = 'cli_command';

    /** @var string TYPE_UNKNOWN */
    public const TYPE_UNKNOWN = 'unknown';

    /**
     * Gets the origin type.
     *
     * @return string
     */
    public function getType(): string;

    /**
     * Gets the origin payload.
     *
     * e.g. web request GET params, cronjob name, REST-API endpoint etc...
     *
     * @return string
     */
    public function getDetail(): string;
}
