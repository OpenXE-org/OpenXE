<?php

declare(strict_types=1);

namespace Xentral\Modules\GoogleApi\Service;

use Xentral\Modules\GoogleApi\Data\GoogleCredentialsData;

interface GoogleCredentialsServiceInterface
{
    /** @var string KEY_CLIENT_ID */
    public const KEY_CLIENT_ID = 'googleapi_client_id';

    /** @var string KEY_CLIENT_SECRET */
    public const KEY_CLIENT_SECRET = 'googleapi_client_secret';

    /** @var string KEY_REDIRECT_URI */
    public const KEY_REDIRECT_URI = 'googleapi_redirect_uri';

    /**
     * @return GoogleCredentialsData
     */
    public function getCredentials(): GoogleCredentialsData;

    /**
     * @return bool
     */
    public function existCredentials(): bool;

    /**
     * @param GoogleCredentialsData $account
     *
     * @return void
     */
    public function saveCredentials(GoogleCredentialsData $account): void;

    /**
     * @return void
     */
    public function deleteCredentials(): void;
}
