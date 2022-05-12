# Profiler

```php
<?php

/** @var \Xentral\Components\Database\Profiler\Profiler $profiler */
$profiler = $container->get('DatabaseProfiler');
$profiler->setActive(true);

$database->fetchAll($sql);

var_dump($profiler->getContexts());
```