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

use core_privacy\local\metadata\null_provider;

/**
 * Privacy Subsystem for local_zohoflow — no personal data stored.
 *
 * @package   local_zohoflow
 * @category  privacy
 */
class provider implements null_provider {
    /**
     * Get the language string identifier for the privacy statement.
     *
     * @return string
     */
    public static function get_reason(): string {
        return 'privacy:metadata';
    }
}
