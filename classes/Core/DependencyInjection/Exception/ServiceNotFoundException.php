<?php

namespace Xentral\Core\DependencyInjection\Exception;

use RuntimeException;

class ServiceNotFoundException extends RuntimeException implements ContainerExceptionInterface
{
}
