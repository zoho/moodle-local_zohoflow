<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Profile field manager for local_zohoflow plugin.
 *
 * @package    local_zohoflow
 * @author     Zoho Flow <support@zohoflow.com>
 * @copyright  2025, Zoho Corporation Pvt. Ltd.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_zohoflow\course;

use local_zohoflow\webhook\webhook_manager;

/**
 * Handles profile field data retrieval for users.
 */
class course_manager {

    /**
     * Get detailed information about a course.
     *
     * @param int $courseid The course ID.
     * @return array The course details including category data.
     */
    public static function get_course_details($courseid) {
        global $CFG;
        require_once($CFG->dirroot . '/course/lib.php');

        $course = get_course($courseid);
        $coursedata = (array)$course;

        $category = \core_course_category::get($course->category, IGNORE_MISSING);
        if ($category) {
            $coursedata['category'] = [
                'id' => $category->id,
                'name' => $category->name,
                'idnumber' => $category->idnumber,
            ];
        }

        return $coursedata;
    }

    /**
     * Handle course_created event payload.
     *
     * @param \core\event\base $event The Moodle event.
     * @return void
     */
    public static function payload_user_course_created(\core\event\base $event) {
        global $DB;
        if ($event->eventname === '\core\event\course_updated') {
            $webhooks = webhook_manager::get_all_event_webhooks('course_created');
            if (!empty($webhooks)) {
                $returndata = [
                    "event" => "course_created",
                    "eventname" => $event->eventname,
                    "component" => $event->component,
                    "action" => $event->action,
                    "target" => $event->target,
                    "objectid" => $event->objectid,
                    "objecttable" => $event->objecttable,
                    "crud" => $event->crud,
                    "edulevel" => $event->edulevel,
                    "contextid" => $event->contextid,
                    "contextlevel" => $event->contextlevel,
                    "contextinstanceid" => $event->contextinstanceid,
                    "userid" => $event->userid,
                    "courseid" => $event->courseid,
                    "relateduserid" => $event->relateduserid,
                    "anonymous" => $event->anonymous,
                    "other" => $event->other,
                    "timecreated" => $event->timecreated,
                    "data" => self::get_course_details($event->courseid),
                ];
                foreach ($webhooks as $webhook) {
                    webhook_manager::send($webhook->url, $returndata);
                }
            }
        }
    }

    /**
     * Handle course_updated event payload.
     *
     * @param \core\event\base $event The Moodle event.
     * @return void
     */
    public static function payload_user_course_updated(\core\event\base $event) {
        global $DB;
        if ($event->eventname === '\core\event\course_updated' ) {
            $webhooks = webhook_manager::get_all_event_webhooks('course_updated');
            if (!empty($webhooks) ) {
                $returndata = [
                    "event" => "course_updated",
                    "eventname" => $event->eventname,
                    "component" => $event->component,
                    "action" => $event->action,
                    "target" => $event->target,
                    "objectid" => $event->objectid,
                    "objecttable" => $event->objecttable,
                    "crud" => $event->crud,
                    "edulevel" => $event->edulevel,
                    "contextid" => $event->contextid,
                    "contextlevel" => $event->contextlevel,
                    "contextinstanceid" => $event->contextinstanceid,
                    "userid" => $event->userid,
                    "courseid" => $event->courseid,
                    "relateduserid" => $event->relateduserid,
                    "anonymous" => $event->anonymous,
                    "other" => $event->other,
                    "timecreated" => $event->timecreated,
                    "data" => self::get_course_details($event->courseid),
                ];
                foreach ($webhooks as $webhook) {
                    webhook_manager::send($webhook->url, $returndata);
                }
            }
        }
    }
}
