<?php

namespace Sabre\VObject\Property\ICalendar;

use Sabre\VObject\Component\VCalendar;
use Sabre\VObject\Reader;
use Sabre\VObject\TestCase;

class RecurTest extends TestCase {

    function testParts() {

        $vcal = new VCalendar();
        $recur = $vcal->add('RRULE', 'FREQ=Daily');

        $this->assertInstanceOf('Sabre\VObject\Property\ICalendar\Recur', $recur);

        $this->assertEquals(['FREQ' => 'DAILY'], $recur->getParts());
        $recur->setParts(['freq'    => 'MONTHLY']);

        $this->assertEquals(['FREQ' => 'MONTHLY'], $recur->getParts());

    }

    /**
     * @expectedException \InvalidArgumentException
     */
    function testSetValueBadVal() {

        $vcal = new VCalendar();
        $recur = $vcal->add('RRULE', 'FREQ=Daily');
        $recur->setValue(new \Exception());

    }

    function testSetValueWithCount() {
        $vcal = new VCalendar();
        $recur = $vcal->add('RRULE', 'FREQ=Daily');
        $recur->setValue(['COUNT' => 3]);
        $this->assertEquals($recur->getParts()['COUNT'], 3);
    }

    function testGetJSONWithCount() {
        $input = 'BEGIN:VCALENDAR
BEGIN:VEVENT
UID:908d53c0-e1a3-4883-b69f-530954d6bd62
TRANSP:OPAQUE
DTSTART;TZID=Europe/Berlin:20160301T150000
DTEND;TZID=Europe/Berlin:20160301T170000
SUMMARY:test
RRULE:FREQ=DAILY;COUNT=3
ORGANIZER;CN=robert pipo:mailto:robert@example.org
END:VEVENT
END:VCALENDAR
';

        $vcal = Reader::read($input);
        $rrule = $vcal->VEVENT->RRULE;
        $count = $rrule->getJsonValue()[0]['count'];
        $this->assertTrue(is_int($count));
        $this->assertEquals(3, $count);
    }

    function testSetSubParts() {

        $vcal = new VCalendar();
        $recur = $vcal->add('RRULE', ['FREQ' => 'DAILY', 'BYDAY' => 'mo,tu', 'BYMONTH' => [0, 1]]);

        $this->assertEquals([
            'FREQ'    => 'DAILY',
            'BYDAY'   => ['MO', 'TU'],
            'BYMONTH' => [0, 1],
        ], $recur->getParts());

    }

    function testGetJSONWithUntil() {
        $input = 'BEGIN:VCALENDAR
BEGIN:VEVENT
UID:908d53c0-e1a3-4883-b69f-530954d6bd62
TRANSP:OPAQUE
DTSTART;TZID=Europe/Berlin:20160301T150000
DTEND;TZID=Europe/Berlin:20160301T170000
SUMMARY:test
RRULE:FREQ=DAILY;UNTIL=20160305T230000Z
ORGANIZER;CN=robert pipo:mailto:robert@example.org
END:VEVENT
END:VCALENDAR
';

        $vcal = Reader::read($input);
        $rrule = $vcal->VEVENT->RRULE;
        $untilJsonString = $rrule->getJsonValue()[0]['until'];
        $this->assertEquals('2016-03-05T23:00:00Z', $untilJsonString);
    }


    function testValidateStripEmpties() {

        $input = 'BEGIN:VCALENDAR
VERSION:2.0
PRODID:foobar
BEGIN:VEVENT
UID:908d53c0-e1a3-4883-b69f-530954d6bd62
TRANSP:OPAQUE
DTSTART;TZID=Europe/Berlin:20160301T150000
DTEND;TZID=Europe/Berlin:20160301T170000
SUMMARY:test
RRULE:FREQ=DAILY;BYMONTH=;UNTIL=20160305T230000Z
ORGANIZER;CN=robert pipo:mailto:robert@example.org
DTSTAMP:20160312T183800Z
END:VEVENT
END:VCALENDAR
';

        $vcal = Reader::read($input);
        $this->assertEquals(
            1,
            count($vcal->validate())
        );
        $this->assertEquals(
            1,
            count($vcal->validate($vcal::REPAIR))
        );

        $expected = 'BEGIN:VCALENDAR
VERSION:2.0
PRODID:foobar
BEGIN:VEVENT
UID:908d53c0-e1a3-4883-b69f-530954d6bd62
TRANSP:OPAQUE
DTSTART;TZID=Europe/Berlin:20160301T150000
DTEND;TZID=Europe/Berlin:20160301T170000
SUMMARY:test
RRULE:FREQ=DAILY;UNTIL=20160305T230000Z
ORGANIZER;CN=robert pipo:mailto:robert@example.org
DTSTAMP:20160312T183800Z
END:VEVENT
END:VCALENDAR
';

        $this->assertVObjEquals(
            $expected,
            $vcal
        );

    }

    function testValidateStripNoFreq() {

        $input = 'BEGIN:VCALENDAR
VERSION:2.0
PRODID:foobar
BEGIN:VEVENT
UID:908d53c0-e1a3-4883-b69f-530954d6bd62
TRANSP:OPAQUE
DTSTART;TZID=Europe/Berlin:20160301T150000
DTEND;TZID=Europe/Berlin:20160301T170000
SUMMARY:test
RRULE:UNTIL=20160305T230000Z
ORGANIZER;CN=robert pipo:mailto:robert@example.org
DTSTAMP:20160312T183800Z
END:VEVENT
END:VCALENDAR
';

        $vcal = Reader::read($input);
        $this->assertEquals(
            1,
            count($vcal->validate())
        );
        $this->assertEquals(
            1,
            count($vcal->validate($vcal::REPAIR))
        );

        $expected = 'BEGIN:VCALENDAR
VERSION:2.0
PRODID:foobar
BEGIN:VEVENT
UID:908d53c0-e1a3-4883-b69f-530954d6bd62
TRANSP:OPAQUE
DTSTART;TZID=Europe/Berlin:20160301T150000
DTEND;TZID=Europe/Berlin:20160301T170000
SUMMARY:test
ORGANIZER;CN=robert pipo:mailto:robert@example.org
DTSTAMP:20160312T183800Z
END:VEVENT
END:VCALENDAR
';

        $this->assertVObjEquals(
            $expected,
            $vcal
        );

    }

}
