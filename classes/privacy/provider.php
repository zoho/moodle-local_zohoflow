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
 * Privacy provider for the local_zohoflow plugin.
 *
 * @package    local_zohoflow
 * @category   privacy
 * @author     Zoho Flow <support@zohoflow.com>
 * @copyright  2025, Zoho Corporation Pvt. Ltd.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_zohoflow\privacy;

use core_privacy\local\metadata\collection;
use core_privacy\local\request;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\userlist;
use core_privacy\local\request\approved_userlist;

/**
 * Privacy Subsystem for local_zohoflow.
 *
 * @package   local_zohoflow
 * @category  privacy
 */
class provider implements
    \core_privacy\local\metadata\provider,
    \core_privacy\local\request\core_userlist_provider,
    \core_privacy\local\request\core_user_data_provider,
    \core_privacy\local\request\plugin\provider {
    /**
     * Describe stored personal data.
     *
     * @param collection $collection
     * @return collection
     */
    public static function get_metadata(collection $collection): collection {
        $collection->add_database_table('local_zohoflow_webhooks', [
            'userid'        => 'privacy:metadata:local_zohoflow_webhooks:userid',
            'name'          => 'privacy:metadata:local_zohoflow_webhooks:name',
            'url'           => 'privacy:metadata:local_zohoflow_webhooks:url',
            'eventtype'     => 'privacy:metadata:local_zohoflow_webhooks:eventtype',
            'meta'          => 'privacy:metadata:local_zohoflow_webhooks:meta',
            'enabled'       => 'privacy:metadata:local_zohoflow_webhooks:enabled',
            'signature'     => 'privacy:metadata:local_zohoflow_webhooks:signature',
            'timecreated'   => 'privacy:metadata:local_zohoflow_webhooks:timecreated',
            'timemodified'  => 'privacy:metadata:local_zohoflow_webhooks:timemodified',
        ], 'privacy:metadata:local_zohoflow_webhooks');

        // External destination for personal data (webhooks).
        $collection->add_external_location_link('webhook', [
            'userid' => 'privacy:metadata:webhook:userid',
            'courseid' => 'privacy:metadata:webhook:courseid',
            'contextid' => 'privacy:metadata:webhook:contextid',
            'contextinstanceid' => 'privacy:metadata:webhook:contextinstanceid',
            'objectid' => 'privacy:metadata:webhook:objectid',
            'objecttable' => 'privacy:metadata:webhook:objecttable',
            'event' => 'privacy:metadata:webhook:event',
            'eventname' => 'privacy:metadata:webhook:eventname',
            'component' => 'privacy:metadata:webhook:component',
            'action' => 'privacy:metadata:webhook:action',
            'target' => 'privacy:metadata:webhook:target',
            'crud' => 'privacy:metadata:webhook:crud',
            'edulevel' => 'privacy:metadata:webhook:edulevel',
            'relateduserid' => 'privacy:metadata:webhook:relateduserid',
            'anonymous' => 'privacy:metadata:webhook:anonymous',
            'other' => 'privacy:metadata:webhook:other',
            'timecreated' => 'privacy:metadata:webhook:timecreated',
            'username' => 'privacy:metadata:webhook:username',
            'firstname' => 'privacy:metadata:webhook:firstname',
            'lastname' => 'privacy:metadata:webhook:lastname',
            'email' => 'privacy:metadata:webhook:email',
            'courses' => 'privacy:metadata:webhook:courses',
            'roles' => 'privacy:metadata:webhook:roles',
            'groups' => 'privacy:metadata:webhook:groups',
            'city' => 'privacy:metadata:webhook:city',
            'country' => 'privacy:metadata:webhook:country',
            'profilefields' => 'privacy:metadata:webhook:profilefields',
        ], 'privacy:metadata:webhook');

        return $collection;
    }

    /**
     * Get list of contexts containing user data.
     *
     * @param int $userid
     * @return contextlist
     */
    public static function get_contexts_for_userid(int $userid): contextlist {
        global $DB;

        $contextlist = new contextlist();

        $sql = "SELECT ctx.id
                  FROM {local_zohoflow_webhooks} w
                  JOIN {context} ctx ON ctx.instanceid = w.userid
                 WHERE w.userid = :userid
                   AND ctx.contextlevel = :contextlevel";

        $params = [
            'userid' => $userid,
            'contextlevel' => CONTEXT_USER,
        ];
        $contextlist->add_from_sql($sql, $params);
        return $contextlist;
    }

    /**
     * Export all user data for the approved contexts.
     *
     * @param approved_contextlist $contextlist
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        global $DB;

        $userid = $contextlist->get_user()->id;

        if (!$contextlist->count()) {
            return;
        }

        $records = $DB->get_records('local_zohoflow_webhooks', ['userid' => $userid]);
        if (!$records) {
            return;
        }

        foreach ($contextlist->get_contextids() as $contextid) {
            $context = \context::instance_by_id($contextid);
            \core_privacy\local\request\writer::with_context($context)
                ->export_data(
                    ['zohoflow_webhooks'],
                    (object)[
                        'webhooks' => array_values($records),
                    ]
                );
        }
    }

    /**
     * Delete user data in approved contexts.
     *
     * @param approved_contextlist $contextlist
     */
    public static function delete_data_for_approved_contexts(approved_contextlist $contextlist) {
        global $DB;

        if (!$contextlist->count()) {
            return;
        }

        $userid = $contextlist->get_user()->id;
        $DB->delete_records('local_zohoflow_webhooks', ['userid' => $userid]);
    }

    /**
     * Delete multiple users' data in a context.
     *
     * @param approved_contextlist $contextlist
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        global $DB;

        if (!$contextlist->count()) {
            return;
        }
        $userid = $contextlist->get_user()->id;
        $DB->delete_records('local_zohoflow_webhooks', ['userid' => $userid]);
    }

    /**
     * Delete data for all users in a given context.
     *
     * @param \context $context
     */
    public static function delete_data_for_all_users_in_context(\context $context) {
        global $DB;

        if ($context->contextlevel == CONTEXT_USER) {
            $userid = $context->instanceid;
            $DB->delete_records('local_zohoflow_webhooks', ['userid' => $userid]);
        }
    }

    /**
     * Get the list of users who have data within a context.
     *
     * @param userlist $userlist The userlist to add user information to.
     */
    public static function get_users_in_context(userlist $userlist) {
        global $DB;

        $context = $userlist->get_context();
        if ($context->contextlevel !== CONTEXT_USER) {
            return;
        }
        $sql = "SELECT userid
                  FROM {local_zohoflow_webhooks}
                 WHERE userid = :userid";
        $params = ['userid' => $context->instanceid];

        $userlist->add_from_sql('userid', $sql, $params);
    }

    /**
     * Delete data for users within a context.
     *
     * @param approved_userlist $userlist The approved context and user information to delete.
     */
    public static function delete_data_for_users(approved_userlist $userlist) {
        global $DB;
        $useridlist = $userlist->get_userids();
        if (empty($useridlist)) {
            return;
        }
        [$insql, $inparams] = $DB->get_in_or_equal($useridlist, SQL_PARAMS_NAMED);
        $DB->delete_records_select('local_zohoflow_webhooks', "userid $insql", $inparams);
    }
}
