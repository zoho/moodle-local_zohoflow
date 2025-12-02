<?php
// This file is part of Moodle - https://moodle.org/.
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Web service function and service definitions for the local_zohoflow plugin.
 *
 * @package    local_zohoflow
 * @category   webservice
 * @copyright  2025, Zoho Corporation Pvt. Ltd.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

 defined('MOODLE_INTERNAL') || die();

$functions = [
    'local_zohoflow_get_roles' => [
        'classname'   => 'local_zohoflow\external\user_service',
        'methodname'  => 'get_roles',
        'classpath'   => 'local/zohoflow/classes/external/user_service.php',
        'description' => 'Get all user roles.',
        'type'        => 'read',
        'ajax'        => false,
        'services'    => ['zoho_flow'],
    ],
    'local_zohoflow_list_profile_fields' => [
        'classname'   => 'local_zohoflow\external\profilefield_service',
        'methodname'  => 'get_profile_fields',
        'classpath'   => 'local/zohoflow/classes/external/profilefield_service.php',
        'description' => 'List all profile fields.',
        'type'        => 'read',
        'ajax'        => false,
        'services'    => ['zoho_flow'],
    ],
    'local_zohoflow_get_user_details_with_profile_fields' => [
        'classname'   => 'local_zohoflow\external\user_service',
        'methodname'  => 'get_user_details_with_profile_fields',
        'classpath'   => 'local/zohoflow/classes/external/user_service.php',
        'description' => 'Get user details including custom profile fields.',
        'type'        => 'read',
        'ajax'        => false,
        'services'    => ['zoho_flow'],
    ],
    'local_zohoflow_list_webhooks' => [
        'classname'   => 'local_zohoflow\external\webhook_service',
        'methodname'  => 'list_webhooks',
        'classpath'   => 'local/zohoflow/classes/external/webhook_service.php',
        'description' => 'List all configured webhooks.',
        'type'        => 'read',
        'capabilities' => '',
        'ajax'        => false,
        'services'    => ['zoho_flow'],
    ],
    'local_zohoflow_add_webhook' => [
        'classname'   => 'local_zohoflow\external\webhook_service',
        'methodname'  => 'add_webhook',
        'classpath'   => 'local/zohoflow/classes/external/webhook_service.php',
        'description' => 'Add a new webhook with name, URL, event type, and optional meta details.',
        'type'        => 'write',
        'capabilities' => '',
        'ajax'        => false,
        'services'    => ['zoho_flow'],
    ],
    'local_zohoflow_delete_webhook' => [
        'classname'   => 'local_zohoflow\external\webhook_service',
        'methodname'  => 'delete_webhook',
        'classpath'   => '',
        'description' => 'Delete a webhook by ID.',
        'type'        => 'write',
        'capabilities' => '',
        'ajax'        => false,
        'services'    => ['zoho_flow'],
    ],
];

$services = [
    'Zoho Flow' => [
        'functions'       => [
            'core_webservice_get_site_info',
            'core_user_get_users_by_field',
            'core_user_get_users',
            'auth_email_signup_user',
            'core_user_create_users',
            'core_user_update_users',
            'enrol_manual_enrol_users',
            'enrol_self_enrol_user',
            'enrol_manual_unenrol_users',
            'core_auth_request_password_reset',
            'core_course_get_courses',
            'core_course_get_courses_by_field',
            'core_course_get_recent_courses',
            'core_course_search_courses',
            'core_course_create_courses',
            'core_course_update_courses',
            'local_zohoflow_get_roles',
            'local_zohoflow_list_profile_fields',
            'local_zohoflow_get_user_details_with_profile_fields',
            'local_zohoflow_list_webhooks',
            'local_zohoflow_add_webhook',
            'local_zohoflow_delete_webhook',
        ],
        'restrictedusers' => 0,
        'enabled'         => 1,
        'shortname'       => 'zoho_flow',
    ],
];
