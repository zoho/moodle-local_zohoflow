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

namespace local_zohoflow\user;

use core_user;
use local_zohoflow\webhook\webhook_manager;

/**
 * Manages user-related event payloads for the Zoho Flow integration.
 *
 * This class listens to Moodle core user events (such as created, updated,
 * deleted, logged in/out, etc.) and builds webhook payloads that are sent
 * to registered Zoho Flow webhook endpoints.
 *
 * @package    local_zohoflow
 * @subpackage external
 * @author     Zoho Flow <support@zohoflow.com>
 * @copyright  2025, Zoho Corporation Pvt. Ltd.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_manager {

    /**
     * Retrieves all roles available in Moodle.
     *
     * @return array List of roles with id, shortname, name, description, and archetype.
     */
    public static function list_all_roles() {
        global $DB;

        $sql = "SELECT id, shortname, name, description, archetype
                  FROM {role}
              ORDER BY sortorder ASC";

        $roles = $DB->get_records_sql($sql);

        $result = [];
        foreach ($roles as $role) {
            $result[] = [
                'id' => $role->id,
                'shortname' => $role->shortname,
                'name' => $role->name,
                'description' => $role->description,
                'archetype' => $role->archetype,
            ];
        }

        return $result;
    }

    /**
     * Check whether a user is valid (exists, not deleted, not suspended).
     *
     * @param int $userid
     * @return bool
     */
    public static function is_valid_user($userid) {
        global $DB;

        if (empty($userid) || !is_numeric($userid)) {
            return false;
        }

        // Check if the user exists and is active.
        return $DB->record_exists_select(
            'user',
            'id = :id AND deleted = 0',
            ['id' => $userid]
        );
    }

    /**
     * Get user details including profile fields.
     *
     * @param int $userid
     * @return array
     * @throws \moodle_exception
     */
    public static function get_user_with_profile_fields($userid) {
        global $DB;
        global $CFG;

        if (!self::is_valid_user($userid)) {
            return [];
        }
        // Get main user record.
        $user = $DB->get_record('user', ['id' => $userid, 'deleted' => 0], '*', MUST_EXIST);

        // Convert user object to associative array.
        $userdata = (array)$user;

        require_once($CFG->dirroot . "/user/lib.php");

        return user_get_user_details_courses($user);
    }

    /**
     * Get enrolment info using Moodle native APIs.
     */
    private static function get_enrolment_details_native($userenrolid) {
        global $DB;
        global $CFG;

        require_once($CFG->libdir . '/enrollib.php');
        require_once($CFG->libdir . '/grouplib.php');
        require_once($CFG->libdir . '/accesslib.php');

        // Get base enrolment info.
        $ue = $DB->get_record('user_enrolments', ['id' => $userenrolid], '*', MUST_EXIST);
        $enrol = $DB->get_record('enrol', ['id' => $ue->enrolid], '*', MUST_EXIST);
        $course = $DB->get_record('course', ['id' => $enrol->courseid], '*', MUST_EXIST);

        $context = \context_course::instance($course->id);

        $roles = get_user_roles($context, $ue->userid, false);

        $groupids = groups_get_user_groups($course->id, $ue->userid);
        $groupdetails = [];
        if (!empty($groupids[0])) {
            list($insql, $params) = $DB->get_in_or_equal($groupids[0]);
            $groupdetails = $DB->get_records_select('groups', "id $insql", $params, '', 'id,name');
        }

        return [
            'userenrolid' => $ue->id,
            'userid' => $ue->userid,
            'courseid' => $course->id,
            'coursename' => $course->fullname,
            'enrolmethod' => $enrol->enrol,
            'status' => ($ue->status == 0 ? 'active' : 'suspended'),
            'timestart' => $ue->timestart,
            'timeend' => $ue->timeend,
            'roles' => array_values($roles),
            'groups' => array_values($groupdetails),
        ];
    }


    /**
     * Handles user_updated event and sends data to registered webhooks.
     *
     * @param \core\event\base $event The triggered user_updated event.
     * @return void
     */
    public static function payload_user_updated(\core\event\base $event) {
        if ($event->eventname === '\core\event\user_updated') {
            $userid = $event->objectid;
            $webhooks = webhook_manager::get_all_event_webhooks('user_updated');
            if (!empty($webhooks) && self::is_valid_user($userid)) {
                $returndata = [
                    "event" => "user_updated",
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
                    "data" => self::get_user_with_profile_fields( $userid),
                ];
                foreach ($webhooks as $webhook) {
                    webhook_manager::send($webhook->url, $returndata);
                }
            }
        }
    }

    /**
     * Handles user_created event and sends data to registered webhooks.
     *
     * @param \core\event\base $event The triggered user_created event.
     * @return void
     */
    public static function payload_user_created(\core\event\base $event) {
        if ($event->eventname === '\core\event\user_created') {
            $userid = $event->objectid;
            $webhooks = webhook_manager::get_all_event_webhooks('user_created');
            if (!empty($webhooks) && self::is_valid_user($userid)) {
                $returndata = [
                    "event" => "user_created",
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
                    "data" => self::get_user_with_profile_fields( $userid),
                ];
                foreach ($webhooks as $webhook) {
                    webhook_manager::send($webhook->url, $returndata);
                }
            }
        }
    }

    /**
     * Handles user_loggedin event and sends data to registered webhooks.
     *
     * @param \core\event\base $event The triggered user_loggedin event.
     * @return void
     */
    public static function payload_user_logged_in(\core\event\base $event) {
        if ($event->eventname === '\core\event\user_loggedin') {
            $userid = $event->objectid;
            $webhooks = webhook_manager::get_all_event_webhooks('user_logged_in');
            if (!empty($webhooks) && self::is_valid_user($userid)) {
                $returndata = [
                    "event" => "user_logged_in",
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
                    "data" => self::get_user_with_profile_fields( $userid),
                ];
                foreach ($webhooks as $webhook) {
                    webhook_manager::send($webhook->url, $returndata);
                }
            }
        }
    }

    /**
     * Handles user_loggedout event and sends data to registered webhooks.
     *
     * @param \core\event\base $event The triggered user_loggedout event.
     * @return void
     */
    public static function payload_user_logged_out(\core\event\base $event) {
        if ($event->eventname === '\core\event\user_loggedout') {
            $userid = $event->objectid;
            $webhooks = webhook_manager::get_all_event_webhooks('user_logged_out');
            if (!empty($webhooks) && self::is_valid_user($userid)) {
                $returndata = [
                    "event" => "user_logged_out",
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
                    "data" => self::get_user_with_profile_fields( $userid),
                ];
                foreach ($webhooks as $webhook) {
                    webhook_manager::send($webhook->url, $returndata);
                }
            }
        }
    }

    /**
     * Handles user_login_failed event and sends data to registered webhooks.
     *
     * @param \core\event\base $event The triggered user_login_failed event.
     * @return void
     */
    public static function payload_user_login_failed(\core\event\base $event) {
        if ($event->eventname === '\core\event\user_login_failed') {
            $webhooks = webhook_manager::get_all_event_webhooks('user_login_failed');
            if (!empty($webhooks)) {
                $returndata = [
                    "event" => "login_failed",
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
                ];
                foreach ($webhooks as $webhook) {
                    webhook_manager::send($webhook->url, $returndata);
                }
            }
        }
    }

    /**
     * Handles user_graded event and sends data to registered webhooks.
     *
     * @param \core\event\base $event The triggered user_graded event.
     * @return void
     */
    public static function payload_user_graded(\core\event\base $event) {
        global $CFG;
        if ($event->eventname === '\core\event\user_graded') {
            $userid = $event->relateduserid;
            $webhooks = webhook_manager::get_all_event_webhooks('user_graded');
            if (!empty($webhooks) && self::is_valid_user($userid)) {
                $user = self::get_user_with_profile_fields( $userid);
                $user['grade'] = $event->get_grade();
                $returndata = [
                    "event" => "user_graded",
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
                    "data" => $user,
                ];
                foreach ($webhooks as $webhook) {
                    $hookmeta = (array)json_decode($webhook->meta);
                    if (!array_key_exists('courseid', $hookmeta) || $event->courseid == $hookmeta['courseid']) {
                        webhook_manager::send($webhook->url, $returndata);
                    }
                }
            }
        }
    }

    /**
     * Handles user_enrolment_created event and sends data to registered webhooks.
     *
     * @param \core\event\base $event The triggered user_enrolment_created event.
     * @return void
     */
    public static function payload_user_enrolment_created(\core\event\base $event) {
        if ($event->eventname === '\core\event\user_enrolment_created') {
            $userid = $event->relateduserid;
            $webhooks = webhook_manager::get_all_event_webhooks('user_enrolment_created');
            if (!empty($webhooks) && self::is_valid_user($userid)) {
                $user = self::get_user_with_profile_fields( $userid);
                $user['enrolment'] = self::get_enrolment_details_native($event->objectid);
                $returndata = [
                    "event" => "user_enrolment_created",
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
                    "data" => $user,
                ];
                foreach ($webhooks as $webhook) {
                    $hookmeta = (array)json_decode($webhook->meta);
                    if (!array_key_exists('courseid', $hookmeta) || $event->courseid == $hookmeta['courseid']) {
                        webhook_manager::send($webhook->url, $returndata);
                    }
                }
            }
        }
    }

    /**
     * Handles user_enrolment_updated event and sends data to registered webhooks.
     *
     * @param \core\event\base $event The triggered user_enrolment_updated event.
     * @return void
     */
    public static function payload_user_enrolment_updated(\core\event\base $event) {
        if ($event->eventname === '\core\event\user_enrolment_updated') {
            $userid = $event->relateduserid;
            $webhooks = webhook_manager::get_all_event_webhooks('user_enrolment_updated');
            if (!empty($webhooks) && self::is_valid_user($userid)) {
                $user = self::get_user_with_profile_fields( $userid);
                $user['enrolment'] = self::get_enrolment_details_native($event->objectid);
                $returndata = [
                    "event" => "user_enrolment_updated",
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
                    "data" => $user,
                ];
                foreach ($webhooks as $webhook) {
                    $hookmeta = (array)json_decode($webhook->meta);
                    if (!array_key_exists('courseid', $hookmeta) || $event->courseid == $hookmeta['courseid']) {
                        webhook_manager::send($webhook->url, $returndata);
                    }
                }
            }
        }
    }

    /**
     * Handles user_enrolment_deleted event and sends data to registered webhooks.
     *
     * @param \core\event\base $event The triggered user_enrolment_deleted event.
     * @return void
     */
    public static function payload_user_enrolment_deleted(\core\event\base $event) {
        if ($event->eventname === '\core\event\user_enrolment_deleted') {
            $userid = $event->relateduserid;
            $webhooks = webhook_manager::get_all_event_webhooks('user_enrolment_deleted');
            if (!empty($webhooks) && self::is_valid_user($userid)) {
                $user = self::get_user_with_profile_fields( $userid);
                $returndata = [
                    "event" => "user_enrolment_deleted",
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
                    "data" => $user,
                ];
                foreach ($webhooks as $webhook) {
                    $hookmeta = (array)json_decode($webhook->meta);
                    if (!array_key_exists('courseid', $hookmeta) || $event->courseid == $hookmeta['courseid']) {
                        webhook_manager::send($webhook->url, $returndata);
                    }
                }
            }
        }
    }

    /**
     * Handles user_course_completed event and sends data to registered webhooks.
     *
     * @param \core\event\base $event The triggered user_course_completed event.
     * @return void
     */
    public static function payload_user_course_completed(\core\event\base $event) {
        if ($event->eventname === '\core\event\course_completed') {
            $userid = $event->relateduserid;
            $webhooks = webhook_manager::get_all_event_webhooks('user_course_completed');
            if (!empty($webhooks) && self::is_valid_user($userid)) {
                $user = self::get_user_with_profile_fields( $userid);
                $returndata = [
                    "event" => "user_course_completed",
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
                    "data" => $user,
                ];
                foreach ($webhooks as $webhook) {
                    $hookmeta = (array)json_decode($webhook->meta);
                    if (!array_key_exists('courseid', $hookmeta) || $event->courseid == $hookmeta['courseid']) {
                        webhook_manager::send($webhook->url, $returndata);
                    }
                }
            }
        }
    }

    /**
     * Handles course_module_completion_updated event and sends data to registered webhooks.
     *
     * @param \core\event\base $event The triggered course_module_completion_updated event.
     * @return void
     */
    public static function payload_user_course_module_completed(\core\event\base $event) {
        global $DB;
        if ($event->eventname === '\core\event\course_module_completion_updated') {
            $userid = $event->relateduserid;
            $cmid = $event->contextinstanceid;
            $courseid = $event->courseid;
            $webhooks = webhook_manager::get_all_event_webhooks('user_course_module_completed');
            if (!empty($webhooks) && self::is_valid_user($userid)) {
                $user = self::get_user_with_profile_fields( $userid);

                $cm = get_coursemodule_from_id(null, $cmid, 0, false, MUST_EXIST);
                $user['moduleinstance'] = $DB->get_record($cm->modname, ['id' => $cm->instance]);

                $returndata = [
                    "event" => "user_course_module_completed",
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
                    "data" => $user,
                ];
                foreach ($webhooks as $webhook) {
                    $hookmeta = (array)json_decode($webhook->meta);
                    if (!array_key_exists('courseid', $hookmeta) || $event->courseid == $hookmeta['courseid']) {
                        webhook_manager::send($webhook->url, $returndata);
                    }
                }
            }
        }
    }
}
