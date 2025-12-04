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
 * Event observers for the local_zohoflow plugin.
 *
 * @package    local_zohoflow
 * @category   event
 * @copyright  2025, Zoho Corporation Pvt. Ltd.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$observers = [
    [
        'eventname'   => '\core\event\user_updated',
        'callback'    => 'local_zohoflow\local\user\user_manager::payload_user_updated',
        'includefile' => '/local/zohoflow/classes/local/user/user_manager.php',
        'priority'    => 0,
    ],
    [
        'eventname'   => '\core\event\user_created',
        'callback'    => 'local_zohoflow\local\user\user_manager::payload_user_created',
        'includefile' => '/local/zohoflow/classes/local/user/user_manager.php',
        'priority'    => 0,
    ],
    [
        'eventname'   => '\core\event\user_loggedin',
        'callback'    => 'local_zohoflow\local\user\user_manager::payload_user_logged_in',
        'includefile' => '/local/zohoflow/classes/local/user/user_manager.php',
        'priority'    => 0,
    ],
    [
        'eventname'   => '\core\event\user_loggedout',
        'callback'    => 'local_zohoflow\local\user\user_manager::payload_user_logged_out',
        'includefile' => '/local/zohoflow/classes/local/user/user_manager.php',
        'priority'    => 0,
    ],
    [
        'eventname'   => '\core\event\user_login_failed',
        'callback'    => 'local_zohoflow\local\user\user_manager::payload_user_login_failed',
        'includefile' => '/local/zohoflow/classes/local/user/user_manager.php',
        'priority'    => 0,
    ],
    [
        'eventname'   => '\core\event\user_graded',
        'callback'    => 'local_zohoflow\local\user\user_manager::payload_user_graded',
        'includefile' => '/local/zohoflow/classes/local/user/user_manager.php',
        'priority'    => 0,
    ],
    [
        'eventname'   => '\core\event\user_enrolment_created',
        'callback'    => 'local_zohoflow\local\user\user_manager::payload_user_enrolment_created',
        'includefile' => '/local/zohoflow/classes/local/user/user_manager.php',
        'priority'    => 0,
    ],
    [
        'eventname'   => '\core\event\user_enrolment_updated',
        'callback'    => 'local_zohoflow\local\user\user_manager::payload_user_enrolment_updated',
        'includefile' => '/local/zohoflow/classes/local/user/user_manager.php',
        'priority'    => 0,
    ],
    [
        'eventname'   => '\core\event\user_enrolment_deleted',
        'callback'    => 'local_zohoflow\local\user\user_manager::payload_user_enrolment_deleted',
        'includefile' => '/local/zohoflow/classes/local/user/user_manager.php',
        'priority'    => 0,
    ],
    [
        'eventname'   => '\core\event\course_completed',
        'callback'    => 'local_zohoflow\local\user\user_manager::payload_user_course_completed',
        'includefile' => '/local/zohoflow/classes/local/user/user_manager.php',
        'priority'    => 0,
    ],
    [
        'eventname'   => '\core\event\course_module_completion_updated',
        'callback'    => 'local_zohoflow\local\user\user_manager::payload_user_course_module_completed',
        'includefile' => '/local/zohoflow/classes/local/user/user_manager.php',
        'priority'    => 0,
    ],
    [
        'eventname'   => '\core\event\course_created',
        'callback'    => 'local_zohoflow\local\course\course_manager::payload_user_course_created',
        'includefile' => '/local/zohoflow/classes/local/course/course_manager.php',
        'priority'    => 0,
    ],
    [
        'eventname'   => '\core\event\course_updated',
        'callback'    => 'local_zohoflow\local\course\course_manager::payload_user_course_updated',
        'includefile' => '/local/zohoflow/classes/local/course/course_manager.php',
        'priority'    => 0,
    ],
];
