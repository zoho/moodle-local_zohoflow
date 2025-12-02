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

namespace local_zohoflow\webhook;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/local/zohoflow/lib.php');
require_once($CFG->libdir . '/filelib.php');

defined('MOODLE_INTERNAL') || die();

/**
 * Webhook Manager handles CRUD operations for webhooks.
 *
 * @package    local_zohoflow
 */
class webhook_manager {
    /**
     * Add a webhook.
     *
     * @param string $name The webhook name.
     * @param string $url The webhook URL.
     * @param string $eventtype The event type.
     * @param array $meta Optional metadata.
     * @return int The ID of the inserted record.
     */
    public static function add_webhook(string $name, string $url, string $eventtype, $meta = []): int {
        global $DB, $USER;

        $record = new \stdClass();
        $record->name = $name;
        $record->url = $url;
        $record->eventtype = $eventtype;
        $record->userid = $USER->id ?? 0;
        $record->meta = json_encode($meta);
        $record->timecreated = time();
        $record->timemodified = time();

        return $DB->insert_record(LOCAL_ZOHOFLOW_TABLE_WEBHOOKS, $record);
    }

    /**
     * Disable a webhook by URL.
     *
     * @param string $url The webhook URL.
     * @return void
     */
    public static function disable_webhook(string $url) {
        global $DB;

        $sql = "UPDATE {" . LOCAL_ZOHOFLOW_TABLE_WEBHOOKS . "}
                SET enabled = 0,
                    timemodified = :timemodified
                WHERE " . $DB->sql_compare_text('url') . " = :url";

        $params = [
            'url' => $url,
            'timemodified' => time(),
        ];

        $DB->execute($sql, $params);
    }

    /**
     * List all webhooks.
     */
    public static function get_all_webhooks() {
        global $DB;
        return $DB->get_records(LOCAL_ZOHOFLOW_TABLE_WEBHOOKS, null, 'timecreated DESC');
    }

    /**
     * Delete webhook by ID.
     *
     * @param int $id The ID of the webhook to delete.
     * @return bool True if deletion was successful, false otherwise.
     */
    public static function delete_webhook($id) {
        global $DB;
        return $DB->delete_records(LOCAL_ZOHOFLOW_TABLE_WEBHOOKS, ['id' => $id]);
    }

    /**
     * Get all enabled webhooks for a given event type.
     *
     * @param string $eventtype Event type to filter by.
     * @return array Matching webhooks.
     */
    public static function get_all_event_webhooks($eventtype) {
        global $DB;

        $sql = "SELECT *
                FROM {" . LOCAL_ZOHOFLOW_TABLE_WEBHOOKS . "}
                WHERE enabled = 1
                AND " . $DB->sql_compare_text('eventtype') . " = :eventtype";

        $params = ['eventtype' => $eventtype];
        $hooks = $DB->get_records_sql($sql, $params);
        return $hooks;
    }

    /**
     * Send HTTP POST request to a webhook URL with JSON payload.
     *
     * Automatically disables the webhook if HTTP status 410 is returned.
     *
     * @param string $url The webhook endpoint URL.
     * @param array|object $data Payload to send (will be JSON encoded).
     * @return array Returns an associative array containing:
     *               - status (int) HTTP response status code
     *               - response (string) Raw response body
     */
    public static function send($url, $data) {
        $curl = new \curl();
        $options = [
            'CURLOPT_RETURNTRANSFER' => true,
            'CURLOPT_HTTPHEADER' => ['Content-Type: application/json'],
            'CURLOPT_TIMEOUT' => 10, // Timeout in seconds.
            'CURLOPT_CONNECTTIMEOUT' => 5, // Connection timeout.
        ];
        $response = $curl->post($url, json_encode($data), $options);
        $info = $curl->get_info();
        $status = $info['http_code'] ?? 0;

        if ($status == 410) {
            self::disable_webhook($url);
        }

        return [
            'status' => $status,
            'response' => $response,
        ];
    }
}
