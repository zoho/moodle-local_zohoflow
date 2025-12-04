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
 * Version details for local_zohoflow plugin
 *
 * @package    local_zohoflow
 * @subpackage zohoflow
 * @author     Zoho Flow <support@zohoflow.com>
 * @copyright  2025, Zoho Corporation Pvt. Ltd.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_zohoflow\external;

defined('MOODLE_INTERNAL') || die();

use external_function_parameters;
use external_value;
use external_multiple_structure;
use external_single_structure;
use external_api;
use context_system;
use local_zohoflow\local\webhook\webhook_manager;

require_once("$CFG->libdir/externallib.php");

/**
 * Web service endpoints for managing webhooks.
 */
class webhook_service extends external_api {
    /** @var array Allowed event types for webhooks */
    const EVENTTYPES = [
        'user_created',
        'user_updated',
        'user_logged_in',
        'user_logged_out',
        'user_login_failed',
        'user_graded',
        'user_enrolment_created',
        'user_enrolment_updated',
        'user_enrolment_deleted',
        'user_course_completed',
        'user_course_module_completed',
        'course_created',
        'course_updated',
    ];

    /**
     * Define parameters for add_webhook function.
     */
    public static function add_webhook_parameters() {
        return new external_function_parameters([
            'name' => new external_value(PARAM_TEXT, 'Webhook name'),
            'url' => new external_value(PARAM_URL, 'Webhook URL'),
            'eventtype' => new external_value(PARAM_TEXT, 'Event type (user_created, user_updated)'),
            'meta' => new external_single_structure([
                'courseid' => new external_value(PARAM_INT, 'Course ID', VALUE_OPTIONAL),
            ], 'Additional metadata', VALUE_OPTIONAL),
        ]);
    }

    /**
     * Add a new webhook.
     *
     * @param string $name Webhook name
     * @param string $url Webhook URL
     * @param string $eventtype Event type for the webhook
     * @param array $meta Additional metadata such as course ID (optional)
     * @return array Contains inserted webhook ID and operation status
     * @throws \invalid_parameter_exception If event type is invalid
     * @throws \required_capability_exception If user lacks capability
     */
    public static function add_webhook($name, $url, $eventtype, $meta = []) {
        global $DB;

        $params = self::validate_parameters(
            self::add_webhook_parameters(),
            ['name' => $name, 'url' => $url, 'eventtype' => $eventtype, 'meta' => $meta]
        );

        $context = context_system::instance();
        self::validate_context($context);

        if (!has_capability('moodle/site:config', $context)) {
            throw new required_capability_exception($context, 'moodle/site:config', 'nopermissions', '');
        }

        if (!in_array($eventtype, self::EVENTTYPES)) {
            throw new invalid_parameter_exception('Invalid event type: ' . $eventtype);
        }

        // Insert the record via webhook_manager.
        $encodedmeta = $meta;
        $id = webhook_manager::add_webhook($name, $url, $eventtype, $encodedmeta);

        return ['id' => $id, 'status' => 'success'];
    }

    /**
     * Return structure for add_webhook.
     */
    public static function add_webhook_returns() {
        return new external_single_structure([
            'id' => new external_value(PARAM_INT, 'Inserted webhook ID'),
            'status' => new external_value(PARAM_TEXT, 'Operation status'),
        ]);
    }

    /**
     * Parameters for listing webhooks.
     */
    public static function list_webhooks_parameters() {
        return new external_function_parameters([]);
    }

    /**
     * List all stored webhooks.
     *
     * @return array Webhook list
     * @throws \required_capability_exception If user lacks capability
     */
    public static function list_webhooks() {
        $params = self::validate_parameters(self::list_webhooks_parameters(), []);
        $context = context_system::instance();
        self::validate_context($context);

        if (!has_capability('moodle/site:config', $context)) {
            throw new required_capability_exception($context, 'moodle/site:config', 'nopermissions', '');
        }

        $records = webhook_manager::get_all_webhooks();
        return array_values($records);
    }

    /**
     * Return structure for list_webhooks.
     */
    public static function list_webhooks_returns() {
        return new external_multiple_structure(
            new external_single_structure([
                'id' => new external_value(PARAM_INT, 'Webhook ID'),
                'name' => new external_value(PARAM_TEXT, 'Name'),
                'url' => new external_value(PARAM_URL, 'URL'),
                'eventtype' => new external_value(PARAM_TEXT, 'Event type'),
                'meta' => new external_value(PARAM_RAW, 'Meta data (JSON)'),
                'enabled' => new external_value(PARAM_INT, 'Enabled flag'),
                'timecreated' => new external_value(PARAM_INT, 'Created timestamp'),
                'timemodified' => new external_value(PARAM_INT, 'Modified timestamp'),
            ])
        );
    }

    /**
     * Define parameters for delete_webhook function.
     */
    public static function delete_webhook_parameters() {
        return new external_function_parameters([
            'id' => new external_value(PARAM_INT, 'Webhook ID to delete'),
        ]);
    }

    /**
     * Delete a webhook by ID.
     *
     * @param int $id Webhook ID
     * @return array Deletion result
     * @throws \invalid_parameter_exception If webhook id is invalid
     * @throws \required_capability_exception If user lacks capability
     */
    public static function delete_webhook($id) {
        global $DB;

        $params = self::validate_parameters(self::delete_webhook_parameters(), ['id' => $id]);
        $context = context_system::instance();
        self::validate_context($context);

        if (!has_capability('moodle/site:config', $context)) {
            throw new required_capability_exception($context, 'moodle/site:config', 'nopermissions', '');
        }

        // Check if record exists.
        $record = $DB->get_record('local_zohoflow_webhooks', ['id' => $id]);
        if (!$record) {
            throw new \invalid_parameter_exception('Invalid webhook ID.');
        }

        // Delete record.
        $DB->delete_records('local_zohoflow_webhooks', ['id' => $id]);

        return ['status' => 'success', 'deletedid' => $id];
    }

    /**
     * Return structure for delete_webhook.
     */
    public static function delete_webhook_returns() {
        return new external_single_structure([
            'status' => new external_value(PARAM_TEXT, 'Operation status'),
            'deletedid' => new external_value(PARAM_INT, 'Deleted webhook ID'),
        ]);
    }
}
