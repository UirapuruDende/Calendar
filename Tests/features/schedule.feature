Feature: Schedule adding, removing, editting

  Background:
    Given I have calendar created

  Scenario: Inserting new single event into calendar
    When I add new calendar event with data
    | type    | startDate | endDate     | duration | title      | repetition  |
    | single  | -1 hour   | +30 minutes | 90       | Test event |           - |
    Then calendar has 1 events
    And current event has title 'Test event'
    And calendar returns 1 event for current week

  Scenario: Inserting new weekly event into calendar
    When I add new calendar event with data
    | type    | startDate           | endDate             | duration | title      | repetition                |
    | weekly  | 2015-09-01 12:00:00 | 2015-09-30 13:30:00 | 90       | Test event | monday, wednesday, friday |
    Then calendar has 1 events
    And calendar has 13 occurences

  Scenario: Inserting 3 new single events into calendar
    When I add new calendar event with data
      | type    | startDate | endDate      | duration | title         | repetition  |
      | single  | -1 hour   | +30 minutes  | 90       | Test event 1  |           - |
      | single  | -3 hours  | -90 minutes  | 90       | Test event 2  |           - |
      | single  | +1 hour   | +150 minutes | 90       | Test event 3  |           - |
    Then calendar has 3 events
    And current event has title 'Test event 1'
    And calendar returns 3 event for current week

  Scenario: Inserting new weekly events into calendar
    When I add new calendar event with data
      | type    | startDate           | endDate             | duration | title        | repetition                |
      | weekly  | 2015-09-01 12:00:00 | 2015-09-30 13:30:00 | 90       | Test event 1 | monday, wednesday, friday |
      | weekly  | 2015-09-01 12:00:00 | 2015-09-20 13:00:00 | 60       | Test event 2 | monday, friday            |
      | weekly  | 2015-09-01 12:00:00 | 2015-09-30 13:30:00 | 90       | Test event 3 | thursday                  |
      | weekly  | 2015-09-21 12:00:00 | 2015-09-30 13:30:00 | 90       | Test event 4 | monday, wednesday, friday |
      | weekly  | 2015-10-01 12:00:00 | 2015-10-30 13:30:00 | 90       | Test event 5 | thursday                  |
    Then calendar has 5 events
    And there are 32 occurrences
    And calendar returns 3 events for date range from "2015-09-01" to "2015-09-06"
    And calendar returns 4 occurrences for date range from "2015-09-01" to "2015-09-06"
    And calendar returns 3 events for date range from "2015-09-07" to "2015-09-13"
    And calendar returns 6 occurrences for date range from "2015-09-07" to "2015-09-13"
    And calendar returns 3 events for date range from "2015-09-14" to "2015-09-20"
    And calendar returns 6 occurrences for date range from "2015-09-14" to "2015-09-20"
    And calendar returns 3 events for date range from "2015-09-21" to "2015-09-27"
    And calendar returns 7 occurrences for date range from "2015-09-21" to "2015-09-27"
    And calendar returns 2 events for date range from "2015-09-28" to "2015-09-30"
    And calendar returns 4 occurrences for date range from "2015-09-28" to "2015-09-30"

  Scenario: Updating occurrence of single event changes event also event
    When I add new calendar event with data
      | type    | startDate           | endDate             | duration | title      | repetition  |
      | single  | 2015-09-01 12:00:00 | 2015-09-01 13:30:00 | 90       | Test event |           - |
    And I update occurrence '0' of event with title 'Test event' with data in 'single' mode
      | type    | startDate             | endDate             | duration | title         | repetition  |
      | single  | 2015-09-01 11:00:00   | 2015-09-01 13:30:00 | 150      | Updated event |           - |
    Then event with title 'Updated event' has data
      | type    | startDate             | endDate             | duration | title         | repetition  |
      | single  | 2015-09-01 11:00:00   | 2015-09-01 13:30:00 | 150      | Updated event |           - |
    And occurence of single event with title 'Updated event' has data
      | startDate             | endDate             | duration |
      | 2015-09-01 11:00:00   | 2015-09-01 13:30:00 | 150      |

  Scenario: Updating occurrence of weekly event with 'all_exclusive' strategy changes
            whole event and all occurrences without "modified = true" flag.
    When I add new calendar event with data
      | type    | startDate           | endDate             | duration | title      | repetition      |
      | weekly  | 2015-09-01 12:00:00 | 2015-09-13 13:30:00 |       90 | Test event | monday, tuesday, friday |
    And there are 5 occurrences
    And I update occurrence '3' of event with title 'Test event' with data in 'single' mode
      | type    | startDate | endDate     | duration | title              | repetition  |
      | weekly  | -1 hour   | +90 minutes | 150      | Updated occurrence |           - |
    And I update occurrence '4' of event with title 'Test event' with data in 'all_exclusive' mode
      | type    | startDate           | endDate             | duration | title              | repetition  |
      | weekly  | 2015-09-01 12:00:00 | 2015-09-13 13:00:00 | 60       | Updated test event | wednesday, friday |
    Then event with title 'Updated test event' has data
      | type    | startDate           | endDate             | duration | title              | repetition  |
      | weekly  | 2015-09-01 12:00:00 | 2015-09-13 13:00:00 | 60       | Updated test event | wednesday, friday |
    And occurences of event with title 'Updated test event' should have data
      | startDate | endDate     | duration |
      | -1 hour   | +90 minutes | 150      |
      | -1 hour   | +90 minutes | 150      |
      | -1 hour   | +90 minutes | 150      |
      | -1 hour   | +90 minutes | 150      |
      | -1 hour   | +90 minutes | 150      |
#
#  Scenario: Updating occurrence of weekly event with 'all_next' strategy changes
#            whole event and all occurrences after the changed one to new data, but
#            modifies old occurrence to stay as they were (and sets them a modified
#            flag as technically they're changed)
#    When I add new calendar event with data
#      | type    | startDate | endDate     | duration | title      | repetition  |
#      | single  | now       | +90 minutes | 90       | Test event |           - |
#    And I update occurrence '0' with data in 'next_inclusive' mode
#      | type    | startDate | duration | title      | repetition  | modification |
#      | single  | -1 hour   | 90       | Test event |           - | single       |
#    Then event with title 'Test event' has data
#      | type    | startDate | endDate     | duration | title      | repetition  |
#      | single  | now       | +90 minutes | 90       | Test event |           - |
#
#  Scenario: Updating occurrence of weekly event with 'single' strategy changes
#            only updatego occurrence setting a "modified" flag
#    When I add new calendar event with data
#      | type    | startDate | endDate     | duration | title      | repetition  |
#      | single  | now       | +90 minutes | 90       | Test event |           - |
#    And I update occurrence '0' with data in 'single' mode
#      | type    | startDate | duration | title      | repetition  | modification |
#      | single  | -1 hour   | 90       | Test event |           - | single       |
#    Then event with title 'Test event' has data
#      | type    | startDate | endDate     | duration | title      | repetition  |
#      | single  | now       | +90 minutes | 90       | Test event |           - |
#
#  Scenario: Updating startDate and endDate of event adds and deletes occurrences
#
#  Scenario: Updating calendar moves event and all event to new calendar
#
#  Scenario: Deleting occurrence of single event deletes also event (sets flag 'deleted' for both)
#
#  Scenario: Deleting occurrence of weekly event with 'all' strategy deletes
#  whole event and all occurrences (sets flags 'deleted')
#
#  Scenario: Deleting occurrence of weekly event with 'all_next' strategy deletes
#  all future occurrences and modifies events endDate
#
#  Scenario: Deleting occurrence of weekly event with 'single' strategy removes
#  only deleted occurrence (sets flag "deleted")

