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

namespace local_zohoflow\local\profilefield;

/**
 * Handles profile field data retrieval for users.
 */
class profilefield_manager {
    /**
     * Get all user profile fields with category names, sorted by category and sortorder.
     *
     * @return array
     */
    public static function list_all_profile_fields() {
        global $DB;

        $sql = "SELECT f.id,
                       f.name,
                       f.shortname,
                       f.description,
                       f.datatype,
                       f.param1,
                       f.categoryid,
                       c.name AS categoryname,
                       f.sortorder,
                       f.required,
                       f.locked,
                       f.visible,
                       f.forceunique,
                       f.signup
                  FROM {user_info_field} f
                  JOIN {user_info_category} c ON c.id = f.categoryid
              ORDER BY c.sortorder ASC, f.sortorder ASC";

        $fields = $DB->get_records_sql($sql);

        $result = [];
        foreach ($fields as $field) {
            // Convert menu options (param1) into an array if the field is a dropdown.
            $options = [];
            if ($field->datatype === 'menu' && !empty($field->param1)) {
                $options = array_map('trim', explode("\n", $field->param1));
            }

            $result[] = [
                'id' => $field->id,
                'name' => $field->name,
                'shortname' => $field->shortname,
                'description' => $field->description,
                'datatype' => $field->datatype,
                'categoryid' => $field->categoryid,
                'categoryname' => $field->categoryname,
                'sortorder' => $field->sortorder,
                'required' => $field->required,
                'locked' => $field->locked,
                'visible' => $field->visible,
                'forceunique' => $field->forceunique,
                'signup' => $field->signup,
                'options' => $options,
            ];
        }

        return $result;
    }
}
