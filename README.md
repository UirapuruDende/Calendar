[![Build Status](https://travis-ci.org/UirapuruDende/Calendar.svg?branch=master)](https://travis-ci.org/UirapuruDende/Calendar)

[![Sensio](https://insight.sensiolabs.com/projects/ed2857b0-2f75-4fcc-9a7a-b74f712469d4/big.png)](https://insight.sensiolabs.com/projects/ed2857b0-2f75-4fcc-9a7a-b74f712469d4)


# Calendar

Easy to use php calendar component. Calendar consists classes:

* Calendar
* Event
* Occurrence

Installation:

    composer install
    
Running tests:

    ./bin/phpunit
    
    ./bin/behat
    
Rules:
 - Event has Occurrences
 - There's one Occurrence of Event per day
 - Occurrence of an event can be only removed or changed starting hour and duration
 - Occurrence can't overlap on more than 1 day
 - There could be many Events in Calendar
 - Event can be of Single or Weekly type
 - If event is of weekly type it needs to have at least one repetition a week
