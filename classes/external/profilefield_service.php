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
use local_zohoflow\profilefield\profilefield_manager;

require_once("$CFG->libdir/externallib.php");

/**
 * Provides external API endpoints for profile field retrieval.
 *
 * @package   local_zohoflow
 * @category  external
 */
class profilefield_service extends external_api {
    /**
     * Describe parameters for get_profile_fields.
     *
     * @return external_function_parameters
     */
    public static function get_profile_fields_parameters() {
        return new external_function_parameters([]);
    }

    /**
     * Get all user profile fields with category names, sorted by category and sortorder.
     *
     * @return array
     */
    public static function get_profile_fields() {
        return profilefield_manager::list_all_profile_fields();
    }

    /**
     * Describe the return structure.
     *
     * @return external_multiple_structure
     */
    public static function get_profile_fields_returns() {
        return new external_multiple_structure(
            new external_single_structure([
                'id' => new external_value(PARAM_INT, 'Field ID'),
                'name' => new external_value(PARAM_TEXT, 'Field name'),
                'shortname' => new external_value(PARAM_TEXT, 'Short name'),
                'description' => new external_value(PARAM_RAW, 'Field description'),
                'datatype' => new external_value(PARAM_TEXT, 'Data type'),
                'categoryid' => new external_value(PARAM_INT, 'Category ID'),
                'categoryname' => new external_value(PARAM_TEXT, 'Category name'),
                'sortorder' => new external_value(PARAM_INT, 'Field sort order within category'),
                'required' => new external_value(PARAM_INT, 'Whether the field is required'),
                'locked' => new external_value(PARAM_INT, 'Whether the field is locked'),
                'visible' => new external_value(PARAM_INT, 'Visibility setting'),
                'forceunique' => new external_value(PARAM_INT, 'Whether values must be unique'),
                'signup' => new external_value(PARAM_INT, 'Whether shown on signup form'),
                'options' => new external_multiple_structure(
                    new external_value(PARAM_TEXT, 'Dropdown option value'),
                    'Dropdown options (for menu type fields)',
                    VALUE_OPTIONAL
                ),
            ])
        );
    }
}
