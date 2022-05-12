<?php

use Xentral\Modules\SuperSearch\Scheduler\SuperSearchDiffIndexTask;

try {
  /** @var SuperSearchDiffIndexTask $supersearchDiffIndexTask */
  $supersearchDiffIndexTask = $app->Container->get('SuperSearchDiffIndexTask');
  $supersearchDiffIndexTask->execute();
  $supersearchDiffIndexTask->cleanup();

} catch (\Exception $exception) {
  $supersearchDiffIndexTask->cleanup();
  throw $exception;
}
