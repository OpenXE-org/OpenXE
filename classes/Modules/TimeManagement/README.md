# Time-Management-Module

## Description

The time-management module handles all vacation and illness requests off the employees.
It is based (and replaces in certain places) the older module 'Mitarbeiterzeiterfassung'.

### Create new instance

```php
/** @var \Xentral\Modules\TimeManagement\TimeManagementModule $timeManagement */
$timeManagement = $container->get('TimeManagementModule');
```

A hook is provided in mitarbeiterzeiterfassung.php to intervene in the process of changing the state of a day:
```php
$this->app->erp->RunHook(
          'timemanagement_after_day_status_change',
          6,
          $addressId,
          $fromDate,
          $tillDate,
          $halfday,
          $statusOldType,
          $statusWishType
        );
```

## Open issues

- half days: It is not possible to differentiate between morning and afternoon
- the module is designed for vacation and illnesses. It does not handle unpaid vacation or absent days like the old module
- it is not designed for shiftworking over more than one day
- if someone only works half-days he has to take a whole day of vacation. This seems not to be correct and must be changed in future.
