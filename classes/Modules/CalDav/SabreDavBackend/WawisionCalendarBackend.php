<?php

namespace Xentral\Modules\CalDav\SabreDavBackend;

use DateTime;
use Exception;
use InvalidArgumentException;
use Sabre\CalDAV;
use Sabre\CalDAV\Backend\AbstractBackend;
use Sabre\CalDAV\Backend\SchedulingSupport;
use Sabre\CalDAV\Backend\SharingSupport;
use Sabre\CalDAV\Backend\SubscriptionSupport;
use Sabre\CalDAV\Backend\SyncSupport;
use Sabre\DAV;
use Sabre\DAV\Exception\BadRequest;
use Sabre\DAV\Exception\NotImplemented;
use Sabre\DAV\PropPatch;
use Sabre\DAV\Xml\Element\Sharee;
use Sabre\VObject;
use Xentral\Components\Database\Database;


function my_log($data)
{
    //file_put_contents("/home/dakhno/caldavlog.txt", "$data\n", FILE_APPEND);
}

/**
 * PDO CalDAV backend
 *
 * This backend is used to store calendar-data in a PDO database, such as
 * sqlite or MySQL
 *
 * @copyright Copyright (C) fruux GmbH (https://fruux.com/)
 * @author    Evert Pot (http://evertpot.com/)
 * @license   http://sabre.io/license/ Modified BSD License
 */
class WawisionCalendarBackend extends AbstractBackend
    implements
    SyncSupport
{
    const OPERATION_DELETED = 'deleted';
    const OPERATION_ADDED = 'added';
    const OPERATION_MODIFIED = 'modified';

    /**
     * Database
     *
     * @var Database
     */
    protected
        $db;

    /**
     * The table name that will be used for calendars instances.
     *
     * A single calendar can have multiple instances, if the calendar is
     * shared.
     *
     * @var string
     */
    public
        $calendarInstancesTableName = 'calendarinstances';

    /**
     * The table name that will be used for calendar objects
     *
     * @var string
     */
    public
        $calendarObjectTableName = 'kalender_event';


    /**
     * Creates the backend
     *
     * @param Database $db
     */
    function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Returns a list of calendars for a principal.
     *
     * Every project is an array with the following keys:
     *  * id, a unique id that will be used by other functions to modify the
     *    calendar. This can be the same as the uri or a database key.
     *  * uri. This is just the 'base uri' or 'filename' of the calendar.
     *  * principaluri. The owner of the calendar. Almost always the same as
     *    principalUri passed to this method.
     *
     * Furthermore it can contain webdav properties in clark notation. A very
     * common one is '{DAV:}displayname'.
     *
     * Many clients also require:
     * {urn:ietf:params:xml:ns:caldav}supported-calendar-component-set
     * For this property, you can just return an instance of
     * Sabre\CalDAV\Xml\Property\SupportedCalendarComponentSet.
     *
     * If you return {http://sabredav.org/ns}read-only and set the value to 1,
     * ACL will automatically be put in read-only mode.
     *
     * @param string $principalUri
     *
     * @return array
     */
    function getCalendarsForUser($principalUri)
    {
        $synctoken = (int)$this->db->fetchValue('SELECT COALESCE(MAX(id),0) FROM caldav_changes') + 1;
        $calendars = [
            [
                "id"                                                                 => [0, 0],
                "uri"                                                                => "xentral",
                "principaluri"                                                       => $principalUri,
                "{DAV:}displayname"                                                  => "Xentral Kalender",
                "components"                                                         => ["VEVENT"],
                '{' . CalDAV\Plugin::NS_CALDAV . '}supported-calendar-component-set' => new CalDAV\Xml\Property\SupportedCalendarComponentSet([
                    "VEVENT",
                ]),
                "{http://apple.com/ns/ical/}calendar-color"                          => "#42b8c4",
                "share-resource-uri"                                                 => '/ns/share/0',
                "{http://calendarserver.org/ns/}getctag"                             => "http://sabre.io/ns/sync/$synctoken",
                "{http://sabredav.org/ns}sync-token"                                 => $synctoken,
                "{http://apple.com/ns/ical/}calendar-order"                          => "0",
                "share-access"                                                       => "1",
                "{urn:ietf:params:xml:ns:caldav}calendar-description"                => "Xentral Kalender",
                "{urn:ietf:params:xml:ns:caldav}calendar-timezone"                   => null,
            ],
        ];

        return $calendars;
    }

    /**
     * Creates a new calendar for a principal.
     *
     * If the creation was a success, an id must be returned that can be used
     * to reference this calendar in other methods, such as updateCalendar.
     *
     * @param string $principalUri
     * @param string $calendarUri
     * @param array  $properties
     *
     * @return string
     *
     * @throws NotImplemented
     */
    function createCalendar($principalUri, $calendarUri, array $properties)
    {
        throw new NotImplemented('createCalendar not implemented');
    }

    /**
     * Updates properties for a calendar.
     *
     * The list of mutations is stored in a Sabre\DAV\PropPatch object.
     * To do the actual updates, you must tell this object which properties
     * you're going to process with the handle() method.
     *
     * Calling the handle method is like telling the PropPatch object "I
     * promise I can handle updating this property".
     *
     * Read the PropPatch documentation for more info and examples.
     *
     * @param mixed     $calendarId
     * @param PropPatch $propPatch
     *
     * @return void
     * @throws NotImplemented
     */
    function updateCalendar($calendarId, PropPatch $propPatch)
    {
        throw new NotImplemented('updateCalendar not Implemented');
    }

    /**
     * Delete a calendar and all it's objects
     *
     * @param mixed $calendarId
     *
     * @return void
     * @throws NotImplemented
     */
    function deleteCalendar($calendarId)
    {
        throw new NotImplemented('deleteCalendar not implemented');
    }

    /**
     * Returns all calendar objects within a calendar.
     *
     * Every item contains an array with the following keys:
     *   * calendardata - The iCalendar-compatible calendar data
     *   * uri - a unique key which will be used to construct the uri. This can
     *     be any arbitrary string, but making sure it ends with '.ics' is a
     *     good idea. This is only the basename, or filename, not the full
     *     path.
     *   * lastmodified - a timestamp of the last modification time
     *   * etag - An arbitrary string, surrounded by double-quotes. (e.g.:
     *   '  "abcdef"')
     *   * size - The size of the calendar objects, in bytes.
     *   * component - optional, a string containing the type of object, such
     *     as 'vevent' or 'vtodo'. If specified, this will be used to populate
     *     the Content-Type header.
     *
     * Note that the etag is optional, but it's highly encouraged to return for
     * speed reasons.
     *
     * The calendardata is also optional. If it's not returned
     * 'getCalendarObject' will be called later, which *is* expected to return
     * calendardata.
     *
     * If neither etag or size are specified, the calendardata will be
     * used/fetched to determine these numbers. If both are specified the
     * amount of times this is needed is reduced by a great degree.
     *
     * @param mixed $calendarId
     *
     * @return array
     * @throws Exception
     */
    function getCalendarObjects($calendarId)
    {
        $calendarEvents = $this->db->fetchAll('SELECT id, bezeichnung, von, bis, uri, allDay from kalender_event');
        $events = [];
        foreach ($calendarEvents as $event) {
            $id = $event["id"];
            $uri = $event["uri"];
            $data = $this->getICS($event['von'], $event['bis'], $event["bezeichnung"], $uri, $id, $uri,
                $event['allDay']);
            $data = $data["calendardata"];
            $events[] = [
                "id"           => $id,
                "uri"          => $uri,
                "lastmodified" => 0,
                "etag"         => '"' . md5($data) . '"',
                "component"    => "vevent",
                "size"         => strlen($data),
                "calendardata" => $data,
            ];
        }

        return $events;

    }

    /**
     * @param $von
     * @param $bis
     * @param $name
     * @param $uid
     * @param $id
     * @param $uri
     * @param $allDay
     *
     * @return array
     * @throws Exception
     */
    function getICS($von, $bis, $name, $uid, $id, $uri, $allDay)
    {
        $event = [
            'SUMMARY' => $name,
            'UID'     => $uid,
        ];
        if ($allDay) {
            $dateTimeVon = new DateTime($von);
            $event['DTSTART'] = $dateTimeVon->format("Ymd");
        } else {
            $event['DTSTART'] = new DateTime($von);
            $event['DTEND'] = new DateTime($bis);
        }

        $vcalendar = new VObject\Component\VCalendar([
            'VEVENT' => $event,
        ]);

        $ics = $vcalendar->serialize();
        $etag = md5("{$von}{$bis}{$name}{$allDay}");

        return [
            "calendardata" => $ics,
            "uri"          => $uri,
            "id"           => (int)$id,
            "etag"         => '"' . $etag . '"',
            "component"    => "vevent",
            "size"         => strlen($ics),
            "lastmodified" => 0,
        ];
    }

    /**
     * Returns information from a single calendar object, based on it's object
     * uri.
     *
     * The object uri is only the basename, or filename and not a full path.
     *
     * The returned array must have the same keys as getCalendarObjects. The
     * 'calendardata' object is required here though, while it's not required
     * for getCalendarObjects.
     *
     * This method must return null if the object did not exist.
     *
     * @param mixed  $calendarId
     * @param string $objectUri
     *
     * @return array|null
     * @throws Exception
     */

    function getCalendarObject($calendarId, $objectUri)
    {
        $query = $this->db->fetchRow('SELECT id, bezeichnung, von, bis, allDay, uid from kalender_event WHERE uri=:uri;',
            ['uri' => $objectUri]);
        if (!$query) {
            return null;
        }

        $ics = $this->getICS($query['von'], $query['bis'], $query['bezeichnung'], $query['uid'], $query['id'],
            $objectUri, $query['allDay']);

        return $ics;
    }

    /**
     * Returns a list of calendar objects.
     *
     * This method should work identical to getCalendarObject, but instead
     * return all the calendar objects in the list as an array.
     *
     * If the backend supports this, it may allow for some speed-ups.
     *
     * @param mixed $calendarId
     * @param array $uris
     *
     * @return array
     */

    /**
     * Creates a new calendar object.
     *
     * The object uri is only the basename, or filename and not a full path.
     *
     * It is possible return an etag from this function, which will be used in
     * the response to this PUT request. Note that the ETag must be surrounded
     * by double-quotes.
     *
     * However, you should only really return this ETag if you don't mangle the
     * calendar-data. If the result of a subsequent GET to this object is not
     * the exact same as this request body, you should omit the ETag.
     *
     * @param mixed  $calendarId
     * @param string $objectUri
     * @param string $calendarData
     *
     * @return string|null
     * @throws Exception
     */
    function createCalendarObject($calendarId, $objectUri, $calendarData)
    {
        $event = VObject\Reader::read($calendarData);
        $event = $event->VEVENT;

        $start = $event->DTSTART[0];
        if ($start->hasTime() && isset($event->DTEND)) {
            $allDay = 0;
            $start = new DateTime($start);
            $start = $start->format('Y-m-d H:i:s');
            $end = $event->DTEND[0];
            $end = (new DateTime($end))->format('Y-m-d H:i:s');
        } else {
            $allDay = 1;
            $start = new DateTime($start);
            $start = $start->format('Y-m-d H:i:s');
            $end = $start;
        }


        $summary = $event->SUMMARY[0]->getValue();

        $uid = $event->UID[0]->getValue();


        $this->db->perform('INSERT INTO kalender_event (bezeichnung, von, bis, public, uri, uid, allDay) 
                              VALUES (:bezeichnung, :start, :end, :public, :uri, :uid, :allDay);',
            [
                'bezeichnung' => $summary,
                'start'       => $start,
                'end'         => $end,
                'public'      => 1,
                'uri'         => $objectUri,
                'uid'         => $uid,
                'allDay'      => $allDay,
            ]);

        $this->addChange($objectUri, self::OPERATION_ADDED);

        $ics = $this->getICS($start, $end, $summary, $uid, 0, $objectUri, $allDay);

        return $ics['etag'];
    }

    /**
     * Updates an existing calendarobject, based on it's uri.
     *
     * The object uri is only the basename, or filename and not a full path.
     *
     * It is possible return an etag from this function, which will be used in
     * the response to this PUT request. Note that the ETag must be surrounded
     * by double-quotes.
     *
     * However, you should only really return this ETag if you don't mangle the
     * calendar-data. If the result of a subsequent GET to this object is not
     * the exact same as this request body, you should omit the ETag.
     *
     * @param mixed  $calendarId
     * @param string $objectUri
     * @param string $calendarData
     *
     * @return string|null
     *
     * @throws Exception
     */
    function updateCalendarObject($calendarId, $objectUri, $calendarData)
    {
        if (!is_array($calendarId)) {
            throw new InvalidArgumentException('The value passed to $calendarId is expected to be an array with a calendarId and an instanceId');
        }

        $event = VObject\Reader::read($calendarData);
        $event = $event->VEVENT;


        $start = $event->DTSTART[0];
        if ($start->hasTime() && isset($event->DTEND)) {
            $allDay = 0;
            $start = new DateTime($start);
            $start = $start->format('Y-m-d H:i:s');
            $end = $event->DTEND[0];
            $end = new DateTime($end);
            $end = $end->format('Y-m-d H:i:s');
        } else {
            $allDay = 1;
            $start = new DateTime($start);
            $start = $start->format('Y-m-d H:i:s');
            $end = $start;
        }


        $summary = $event->SUMMARY[0]->getValue();

        $uid = $event->UID[0]->getValue();

        $this->db->perform(
            'UPDATE kalender_event SET von=:start, bis=:end, bezeichnung=:desc, allDay=:allDay, uid=:uid WHERE uri=:uri',
            [
                'start'  => $start,
                'end'    => $end,
                'desc'   => $summary,
                'allDay' => $allDay,
                'uri'    => $objectUri,
                'uid' => $uid
            ]
        );

        $ics = $this->getICS($start, $end, $summary, $uid, 0, $objectUri, $allDay);

        $this->addChange($objectUri, self::OPERATION_MODIFIED);

        return $ics['etag'];
    }

    /**
     * Deletes an existing calendar object.
     *
     * The object uri is only the basename, or filename and not a full path.
     *
     * @param mixed  $calendarId
     * @param string $objectUri
     *
     * @return void
     */
    function deleteCalendarObject($calendarId, $objectUri)
    {
        if (!is_array($calendarId)) {
            throw new InvalidArgumentException('The value passed to $calendarId is expected to be an array with a calendarId and an instanceId');
        }
        $this->db->perform('DELETE FROM kalender_event WHERE uri=:uri;', ['uri' => $objectUri]);

        $this->addChange($objectUri, self::OPERATION_DELETED);
    }

    /**
     * Performs a calendar-query on the contents of this calendar.
     *
     * The calendar-query is defined in RFC4791 : CalDAV. Using the
     * calendar-query it is possible for a client to request a specific set of
     * object, based on contents of iCalendar properties, date-ranges and
     * iCalendar component types (VTODO, VEVENT).
     *
     * This method should just return a list of (relative) urls that match this
     * query.
     *
     * The list of filters are specified as an array. The exact array is
     * documented by \Sabre\CalDAV\CalendarQueryParser.
     *
     * Note that it is extremely likely that getCalendarObject for every path
     * returned from this method will be called almost immediately after. You
     * may want to anticipate this to speed up these requests.
     *
     * This method provides a default implementation, which parses *all* the
     * iCalendar objects in the specified calendar.
     *
     * This default may well be good enough for personal use, and calendars
     * that aren't very large. But if you anticipate high usage, big calendars
     * or high loads, you are strongly adviced to optimize certain paths.
     *
     * The best way to do so is override this method and to optimize
     * specifically for 'common filters'.
     *
     * Requests that are extremely common are:
     *   * requests for just VEVENTS
     *   * requests for just VTODO
     *   * requests with a time-range-filter on a VEVENT.
     *
     * ..and combinations of these requests. It may not be worth it to try to
     * handle every possible situation and just rely on the (relatively
     * easy to use) CalendarQueryValidator to handle the rest.
     *
     * Note that especially time-range-filters may be difficult to parse. A
     * time-range filter specified on a VEVENT must for instance also handle
     * recurrence rules correctly.
     * A good example of how to interpret all these filters can also simply
     * be found in \Sabre\CalDAV\CalendarQueryFilter. This class is as correct
     * as possible, so it gives you a good idea on what type of stuff you need
     * to think of.
     *
     * This specific implementation (for the PDO) backend optimizes filters on
     * specific components, and VEVENT time-ranges.
     *
     * @param mixed $calendarId
     * @param array $filters
     *
     * @return array
     */
    function calendarQuery($calendarId, array $filters)
    {
        if (!is_array($calendarId)) {
            throw new InvalidArgumentException('The value passed to $calendarId is expected to be an array with a calendarId and an instanceId');
        }
        list($calendarId, $instanceId) = $calendarId;

        $componentType = null;
        $requirePostFilter = true;
        $timeRange = null;

        // if no filters were specified, we don't need to filter after a query
        if (!$filters['prop-filters'] && !$filters['comp-filters']) {
            $requirePostFilter = false;
        }

        // Figuring out if there's a component filter
        if (count($filters['comp-filters']) > 0 && !$filters['comp-filters'][0]['is-not-defined']) {
            $componentType = $filters['comp-filters'][0]['name'];

            // Checking if we need post-filters
            if (!$filters['prop-filters'] && !$filters['comp-filters'][0]['comp-filters'] && !$filters['comp-filters'][0]['time-range'] && !$filters['comp-filters'][0]['prop-filters']) {
                $requirePostFilter = false;
            }
            // There was a time-range filter
            if ($componentType == 'VEVENT' && isset($filters['comp-filters'][0]['time-range'])) {
                $timeRange = $filters['comp-filters'][0]['time-range'];

                // If start time OR the end time is not specified, we can do a
                // 100% accurate mysql query.
                if (!$filters['prop-filters'] && !$filters['comp-filters'][0]['comp-filters'] && !$filters['comp-filters'][0]['prop-filters'] && (!$timeRange['start'] || !$timeRange['end'])) {
                    $requirePostFilter = false;
                }
            }

        }

        $query = "SELECT uri FROM {$this->calendarObjectTableName} WHERE 1 ";


        return $this->db->fetchCol($query);
    }

    /**
     * Searches through all of a users calendars and calendar objects to find
     * an object with a specific UID.
     *
     * This method should return the path to this object, relative to the
     * calendar home, so this path usually only contains two parts:
     *
     * calendarpath/objectpath.ics
     *
     * If the uid is not found, return null.
     *
     * This method should only consider * objects that the principal owns, so
     * any calendars owned by other principals that also appear in this
     * collection should be ignored.
     *
     * @param string $principalUri
     * @param string $uid
     *
     * @return string|null
     */
    function getCalendarObjectByUID($principalUri, $uid)
    {
        $query = $this->db->fetchRow('SELECT id, bezeichnung, von, bis, allDay, uid from kalender_event WHERE uid=:uid;',
            ['uid' => $uid]);
        if (!$query) {
            return null;
        }

        return "calendars/admin/xentral/{$query['uri']}";
    }

    /**
     * The getChanges method returns all the changes that have happened, since
     * the specified syncToken in the specified calendar.
     *
     * This function should return an array, such as the following:
     *
     * [
     *   'syncToken' => 'The current synctoken',
     *   'added'   => [
     *      'new.txt',
     *   ],
     *   'modified'   => [
     *      'modified.txt',
     *   ],
     *   'deleted' => [
     *      'foo.php.bak',
     *      'old.txt'
     *   ]
     * ];
     *
     * The returned syncToken property should reflect the *current* syncToken
     * of the calendar, as reported in the {http://sabredav.org/ns}sync-token
     * property this is needed here too, to ensure the operation is atomic.
     *
     * If the $syncToken argument is specified as null, this is an initial
     * sync, and all members should be reported.
     *
     * The modified property is an array of nodenames that have changed since
     * the last token.
     *
     * The deleted property is an array with nodenames, that have been deleted
     * from collection.
     *
     * The $syncLevel argument is basically the 'depth' of the report. If it's
     * 1, you only have to report changes that happened only directly in
     * immediate descendants. If it's 2, it should also include changes from
     * the nodes below the child collections. (grandchildren)
     *
     * The $limit argument allows a client to specify how many results should
     * be returned at most. If the limit is not specified, it should be treated
     * as infinite.
     *
     * If the limit (infinite or not) is higher than you're willing to return,
     * you should throw a Sabre\DAV\Exception\TooMuchMatches() exception.
     *
     * If the syncToken is expired (due to data cleanup) or unknown, you must
     * return null.
     *
     * The limit is 'suggestive'. You are free to ignore it.
     *
     * @param mixed  $calendarId
     * @param string $syncToken
     * @param int    $syncLevel
     * @param int    $limit
     *
     * @return array
     */


    function getChangesForCalendar($calendarId, $syncToken, $syncLevel, $limit = null)
    {
        $currentToken = (int)$this->db->fetchValue('SELECT COALESCE(MAX(id), 0) FROM caldav_changes');
        $currentToken++;
        $result = [
            'syncToken'              => $currentToken,
            self::OPERATION_ADDED    => [],
            self::OPERATION_MODIFIED => [],
            self::OPERATION_DELETED  => [],
        ];
        if ($syncToken) {
            $dbChanges = $this->db->fetchAll('SELECT change_type, uri FROM caldav_changes WHERE id >= :token;',
                ['token' => $syncToken]);
            foreach ($dbChanges as $change) {
                $result[$change["change_type"]][] = $change['uri'];
            }
        } else {
            $result['added'] = $this->db->fetchCol('SELECT uri FROM kalender_event;');
        }

        return $result;
    }

    /**
     * Adds a change record to the calendarchanges table.
     *
     * @param string $objectUri
     * @param string added/modified/deleted.
     *
     * @return void
     */
    protected
    function addChange(
        $objectUri,
        $operation
    ) {
        $this->db->perform(
            'INSERT INTO caldav_changes (uri, change_type) VALUES (:uri, :type)',
            [
                'uri'  => $objectUri,
                'type' => $operation,
            ]
        );

        return;
    }

    /**
     * @param mixed $calendarId
     * @param array $uris
     *
     * @return array
     */
    function getMultipleCalendarObjects($calendarId, array $uris)
    {
        return array_filter(parent::getMultipleCalendarObjects($calendarId, $uris), function ($object) {
            return $object !== null;
        });
    }
}
