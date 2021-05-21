<?php

namespace Xentral\Modules\GoogleCalendar\Service;

use Xentral\Modules\Calendar\Data\CalendarEvent;
use Xentral\Modules\Calendar\Data\CalendarEventUser;
use Xentral\Modules\GoogleCalendar\Data\GoogleCalendarEventAttendeeValue;
use Xentral\Modules\GoogleCalendar\Data\GoogleCalendarEventData;
use Xentral\Modules\GoogleCalendar\Wrapper\UserAddressGatewayWrapper;

final class GoogleEventConverter
{
    /** @var UserAddressGatewayWrapper $gateway */
    private $userAddress;

    /**
     * @param UserAddressGatewayWrapper $userAddress
     */
    public function __construct(UserAddressGatewayWrapper $userAddress)
    {
        $this->userAddress = $userAddress;
    }

    /**
     * @param CalendarEvent                $event
     * @param GoogleCalendarEventData|null $googleEvent
     *
     * @return GoogleCalendarEventData
     */
    public function convertToGoogleEvent(CalendarEvent $event, GoogleCalendarEventData $googleEvent = null)
    {
        if ($googleEvent === null) {
            $googleEvent = new GoogleCalendarEventData(0, '', GoogleCalendarEventData::STATUS_CONFIRMED);
        }

        $visibility = GoogleCalendarEventData::VISIBILITY_PRIVATE;
        if ($event->isPublic()) {
            $visibility = GoogleCalendarEventData::VISIBILITY_PUBLIC;
        }

        $googleEvent = $googleEvent->setSummary($event->getTitle())
            ->setDescription($event->getDescription())
            ->setLocation($event->getLocation())
            ->setTime($event->getStart(), $event->getEnd(), $event->isAllDay())
            ->setVisibility($visibility);

        $organizer = $event->getOrganizer();
        $organizerAddress = $organizer->getAddressId();
        $creator = $event->getCreator();
        $creatorAddress = $creator->getAddressId();

        if ($organizer !== null && $organizerAddress > 0 && $organizerAddress !== $creatorAddress)
        {
            $organizerMail = $this->userAddress->getEmailByAddress($organizer->getAddressId());
            if ($organizerMail !== '') {
                $orgaAttendee = new GoogleCalendarEventAttendeeValue($organizerMail);
                $googleEvent = $googleEvent->setOrganizer($orgaAttendee);
            }
        }

        $attendees = $event->getAllUsers();
        foreach ($attendees as $attendee) {
            $address = $attendee->getAddressId();
            if ($address === $creator->getAddressId() || $address === $organizer->getAddressId()) {
                continue;
            }
            $attendeeMail = $this->userAddress->getEmailByAddress($attendee->getAddressId());
            if ($attendeeMail !== '') {
                $googleEvent = $googleEvent->addAttendee($attendeeMail);
            }
        }

        return $googleEvent;
    }

    /**
     * @param GoogleCalendarEventData $googleEvent
     * @param CalendarEvent|null      $event
     *
     * @return CalendarEvent
     */
    public function convertToEvent(
        GoogleCalendarEventData $googleEvent,
        CalendarEvent $event = null
    ) {
        if ($event === null) {
            $event = new CalendarEvent(0, 0, 'Google Calendar Event');
        }
        $event->setTitle($googleEvent->getSummary());
        $event->setDescription($googleEvent->getDescription());
        $event->setStart($googleEvent->getTime()->getBeginning());
        $event->setEnd($googleEvent->getTime()->getEnd());
        $event->setLocation($googleEvent->getLocation());
        $public = true;
        if ($googleEvent->getVisibility() === GoogleCalendarEventData::VISIBILITY_PRIVATE) {
            $public = false;
        }
        $event->setPublic($public);
        $event->setAllDay($googleEvent->getTime()->isWholeday());

        $creator = $this->transformGoogleEventAttendeeToEventUser($googleEvent->getCreator());
        $event->setCreator($creator);
        $organizer = $this->transformGoogleEventAttendeeToEventUser($googleEvent->getOrganizer());
        $event->setOrganizer($organizer);

        foreach ($googleEvent->getAttendees() as $attendee) {
            if (!$attendee->isAttending()) {
                continue;
            }
            $user = $this->transformGoogleEventAttendeeToEventUser($attendee);
            $event->addAttendee($user);
        }

        return $event;
    }

    /**
     * @param GoogleCalendarEventAttendeeValue $attendee
     * @param CalendarEventUser|null           $user
     *
     * @return CalendarEventUser
     */
    public function transformGoogleEventAttendeeToEventUser(
        GoogleCalendarEventAttendeeValue $attendee,
        CalendarEventUser $user = null
    ) {
        if ($user === null) {
            $user = new CalendarEventUser();
        }

        $email = $attendee->getEmail();
        $addressId = $this->userAddress->findAddressByEmail($email);
        $userId = $this->userAddress->getUserByAddress($addressId);
        $user->setUserId($userId);
        $user->setAddressId($addressId);
        $user->setEmail($email);

        return $user;
    }
}
