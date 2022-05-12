# Usage

## dependency injection via LoggerAwareTrait

For logging inside your services, `use` the `Xentral\Components\Logger\LoggerAwareTrait`.
A logger instance gets injected into your service on creation. No need to put the Logger in the constructor!
Inside the class access the logger via `$this->logger`.

Example:
```php
use Xentral\Components\Logger\LoggerAwareTrait;

class MyService
{
    use LoggerAwareTrait;

    public function __construct($myDependency1, $myDependency2)
    {/*...*/}   

    public function myMethod()
    {
        $this->logger->debug('This is my debug message.');
        $this->logger->error('This is my error message.');
    }
}
```

**IMPORTANT**: This works only for objects created by the service container. For all other uses get a logger 
instance from the service container: `$container->get('logger')`.

## Log Levels

#### EMERGENCY
An emergency error should lead to customer calling the support for "emergency problem".
- immediate action required
- data loss is imminent
- software is no more able to run
- only skilled system administrator or our support can resolve the problem

**"The last call for help before the application dies."**

#### ALERT
Use alert in cases where an administrator would want notification by email or sms.
- less severe than emergency
- problem is persistent
- system administrator can resolve the problem
- e.g. cannot connect database
- e.g. userdata runs out of memory

**"Hey Sysadmin, you must fix this! Otherwise, some/many people are not able to work."**

#### CRITICAL
Unhandled/unknown errors and errors that make a piece of software stop working.
Ideally all CRITICALs should be turned into ERRORs over time.
- component or dependency missing
- unhandled exception (exception error page)
- single page/module/cronjob unable to work persistently

**"Something about this module is broken persistently - developer's attention required."**

#### ERROR
Catchable Error that will not break the application.
Error log messages should give the developer a hint where to search for a problem.
- temporary issue
- error response from another API
- error that could be resolved by the user
- an error log message could occur alongside an "error" message on screen
- might resolve after page reload

**"I want at least this information in the log when an Error gets reported."**

#### WARNING
Exceptional occurrences that are not errors.
- things that are not yet but can become problems in the future
- using fallback scenario becaus of incomplete configuration
- low performance
- a warning log messag could occur alongside a "warning" message on screen

**"It works that way, but it's not ideal - you should chage that."**

#### NOTICE
Notable events that let you understand a user's intention.
- no technical detail or dumps!
- max. one per user action
- a cronjob started/finished
- e.g. "User XY created a new lead."

**"What was the user trying to do before the application crashed."**

#### INFO
Common events that describe a user's intention more detailed.
- more verbose form of NOTICE.

**"Might be helpful to reproduce the user's exact actions."**

#### DEBUG
Detailed debug information.
- retrace the code line by line if needed
- variable dumps
- technical information

**"I don't want to debug their production system, so I tell them to turn on DEBUG level and send me the log."**

## Exceptions

You can log an Exception with the complete description and stack-trace by passing it with the `'exception'`
Key in the `context` parameter.
The keyword `'exception'` is reserved and may only be used for Exceptions or other Throwables.

Example:
```php
try {
    //code
} catch (Exception $e) {
    $this->logger->error('An Exception was thrown.', ['exception' => $e]);
}
```

## Message Variables

You can use Variables in your Message and pass the values in the `context` array.
The variable names must match an array key exactly (case-sensitive).
Variable names may contain characters `A-Z a-z 0-9 _ and .` and must be enclosed by curly braces `{}`.   

Example:
```php
$this->logger->info('{username} created ticket {ticketnr}', ['username' => $userName, 'ticketnr' => $ticketNr]);
```

## Dump

Information that is passed in the context array and is not used as a message variable will appear in the `dump` section
of the log entry. 
If an `exception` is passed in the context array **only** the exception will appear in the dump and no other values.

**IMPORTANT**: Do not leak sensitive data or passwords in the dump!
