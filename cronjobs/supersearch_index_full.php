<?php

use Xentral\Modules\SuperSearch\Scheduler\SuperSearchFullIndexTask;

try {
  /** @var SuperSearchFullIndexTask $supersearchFullIndexTask */
  $supersearchFullIndexTask = $app->Container->get('SuperSearchFullIndexTask');
  $supersearchFullIndexTask->execute();
  $supersearchFullIndexTask->cleanup();

} catch (\Exception $exception) {
  $supersearchFullIndexTask->cleanup();
  throw $exception;
}
