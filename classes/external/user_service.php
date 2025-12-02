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
use external_format_value;
use external_multiple_structure;
use external_single_structure;
use external_api;
use context_system;
use local_zohoflow\user\user_manager;
use core_user;

require_once("$CFG->libdir/externallib.php");

/**
 * Provides external functions for user-related operations in local_zohoflow.
 */
class user_service extends \external_api {

    /**
     * Describe parameters for get_roles.
     *
     * @return external_function_parameters
     */
    public static function get_roles_parameters() {
        return new external_function_parameters([]);
    }

    /**
     * Get all user roles.
     *
     * @return array
     */
    public static function get_roles() {
        return user_manager::list_all_roles();
    }

    /**
     * Describe the return structure.
     *
     * @return external_multiple_structure
     */
    public static function get_roles_returns() {
        return new external_multiple_structure(
            new external_single_structure([
                'id' => new external_value(PARAM_INT, 'Role ID'),
                'shortname' => new external_value(PARAM_TEXT, 'Role short name', VALUE_OPTIONAL),
                'name' => new external_value(PARAM_TEXT, 'Role display name'),
                'description' => new external_value(PARAM_RAW, 'Role description', VALUE_OPTIONAL),
                'archetype' => new external_value(PARAM_TEXT, 'Archetype (e.g., student, teacher)', VALUE_OPTIONAL),
            ])
        );
    }

    /**
     * Define parameters for get_user_details_with_profile_fields.
     *
     * @return external_function_parameters
     */
    public static function get_user_details_with_profile_fields_parameters() {
        return new external_function_parameters([
            'userid' => new external_value(PARAM_INT, 'User ID'),
        ]);
    }

    /**
     * Get user details with profile fields.
     *
     * @param int $userid The user ID.
     * @return array User details with custom profile fields.
     */
    public static function get_user_details_with_profile_fields($userid) {
        return user_manager::get_user_with_profile_fields($userid);
    }

    /**
     * Describe the return structure for get_user_details_with_profile_fields.
     *
     * @return external_single_structure
     */
    public static function get_user_details_with_profile_fields_returns() {
        return self::user_fields();
    }

    /**
     * Create user return value description.
     *
     * @return external_description
     */
    public static function user_fields() {
        $userfields = [
            'id' => new external_value(core_user::get_property_type('id'), 'ID of the user'),
            'username' => new external_value(core_user::get_property_type('username'), 'The username', VALUE_OPTIONAL),
            'firstname' => new external_value(
                core_user::get_property_type('firstname'),
                'The first name(s) of the user',
                VALUE_OPTIONAL
            ),
            'lastname' => new external_value(
                core_user::get_property_type('lastname'),
                'The family name of the user',
                VALUE_OPTIONAL
            ),
            'fullname' => new external_value(
                core_user::get_property_type('firstname'),
                'The fullname of the user',
                VALUE_OPTIONAL
            ),
            'email' => new external_value(
                core_user::get_property_type('email'),
                'An email address - e.g., root@localhost',
                VALUE_OPTIONAL
            ),
            'address' => new external_value(core_user::get_property_type('address'), 'Postal address', VALUE_OPTIONAL),
            'phone1' => new external_value(core_user::get_property_type('phone1'), 'Phone 1', VALUE_OPTIONAL),
            'phone2' => new external_value(core_user::get_property_type('phone2'), 'Phone 2', VALUE_OPTIONAL),
            'department' => new external_value(core_user::get_property_type('department'), 'Department', VALUE_OPTIONAL),
            'institution' => new external_value(core_user::get_property_type('institution'), 'Institution', VALUE_OPTIONAL),
            'idnumber' => new external_value(
                core_user::get_property_type('idnumber'),
                'An arbitrary ID code number, perhaps from the institution',
                VALUE_OPTIONAL
            ),
            'interests' => new external_value(PARAM_TEXT, 'User interests (comma-separated)', VALUE_OPTIONAL),
            'firstaccess' => new external_value(
                core_user::get_property_type('firstaccess'),
                'First access to the site (0 if never)',
                VALUE_OPTIONAL
            ),
            'lastaccess' => new external_value(
                core_user::get_property_type('lastaccess'),
                'Last access to the site (0 if never)',
                VALUE_OPTIONAL
            ),
            'auth' => new external_value(
                core_user::get_property_type('auth'),
                'Authentication plugin used (manual, ldap, etc)',
                VALUE_OPTIONAL
            ),
            'suspended' => new external_value(
                core_user::get_property_type('suspended'),
                'Suspend user account: false enables login, true disables it',
                VALUE_OPTIONAL
            ),
            'confirmed' => new external_value(
                core_user::get_property_type('confirmed'),
                'Active user: 1 if confirmed, 0 otherwise',
                VALUE_OPTIONAL
            ),
            'lang' => new external_value(core_user::get_property_type('lang'), 'Language code such as "en"', VALUE_OPTIONAL),
            'calendartype' => new external_value(
                core_user::get_property_type('calendartype'),
                'Calendar type such as "gregorian"',
                VALUE_OPTIONAL
            ),
            'theme' => new external_value(core_user::get_property_type('theme'), 'Theme name such as "standard"', VALUE_OPTIONAL),
            'timezone' => new external_value(
                core_user::get_property_type('timezone'),
                'Timezone code such as Australia/Perth, or 99 for default',
                VALUE_OPTIONAL
            ),
            'mailformat' => new external_value(
                core_user::get_property_type('mailformat'),
                'Mail format code: 0 for plain text, 1 for HTML',
                VALUE_OPTIONAL
            ),
            'trackforums' => new external_value(
                core_user::get_property_type('trackforums'),
                'Whether the user is tracking forums',
                VALUE_OPTIONAL
            ),
            'description' => new external_value(
                core_user::get_property_type('description'),
                'User profile description',
                VALUE_OPTIONAL
            ),
            'descriptionformat' => new external_format_value(
                core_user::get_property_type('descriptionformat'),
                VALUE_OPTIONAL
            ),
            'city' => new external_value(core_user::get_property_type('city'), 'Home city of the user', VALUE_OPTIONAL),
            'country' => new external_value(
                core_user::get_property_type('country'),
                'Home country code such as AU or CZ',
                VALUE_OPTIONAL
            ),
            'profileimageurlsmall' => new external_value(PARAM_URL, 'Small profile image URL', VALUE_OPTIONAL),
            'profileimageurl' => new external_value(PARAM_URL, 'Large profile image URL', VALUE_OPTIONAL),
            'customfields' => new external_multiple_structure(
                new external_single_structure([
                    'type' => new external_value(PARAM_ALPHANUMEXT, 'Type of the custom field'),
                    'value' => new external_value(PARAM_RAW, 'Stored value of the custom field'),
                    'displayvalue' => new external_value(PARAM_RAW, 'Display value', VALUE_OPTIONAL),
                    'name' => new external_value(PARAM_RAW, 'Name of the custom field'),
                    'shortname' => new external_value(PARAM_RAW, 'Shortname of the custom field'),
                ]),
                'User custom fields (user profile fields)',
                VALUE_OPTIONAL
            ),
            'preferences' => new external_multiple_structure(
                new external_single_structure([
                    'name' => new external_value(PARAM_RAW, 'Preference name'),
                    'value' => new external_value(PARAM_RAW, 'Preference value'),
                ]),
                'User preferences',
                VALUE_OPTIONAL
            ),
        ];
        return new external_single_structure($userfields);
    }
}
