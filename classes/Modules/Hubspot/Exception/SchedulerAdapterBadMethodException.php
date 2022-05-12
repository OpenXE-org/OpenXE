<?php

namespace Xentral\Modules\Hubspot\Exception;

use BadMethodCallException as SplBadMethodCallException;

class SchedulerAdapterBadMethodException extends SplBadMethodCallException implements HubspotExceptionInterface
{

}
