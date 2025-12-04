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

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Zoho Flow connector for Moodle';

$string['privacy:metadata'] = 'Zoho Flow connector data transfer details.';
$string['privacy:metadata:local_zohoflow_webhooks'] = 'Stores webhook configurations created by users for external services.';
$string['privacy:metadata:local_zohoflow_webhooks:enabled'] = 'Whether the webhook is enabled or disabled.';
$string['privacy:metadata:local_zohoflow_webhooks:eventtype'] = 'The event type for which the webhook is triggered.';
$string['privacy:metadata:local_zohoflow_webhooks:meta'] = 'Additional metadata related to the webhook.';
$string['privacy:metadata:local_zohoflow_webhooks:name'] = 'The name of the webhook configuration.';
$string['privacy:metadata:local_zohoflow_webhooks:signature'] = 'Optional secure signature used to verify webhook payload.';
$string['privacy:metadata:local_zohoflow_webhooks:timecreated'] = 'The time when the webhook configuration was created.';
$string['privacy:metadata:local_zohoflow_webhooks:timemodified'] = 'The time when the webhook configuration was last modified.';
$string['privacy:metadata:local_zohoflow_webhooks:url'] = 'The webhook endpoint URL where data is sent.';
$string['privacy:metadata:local_zohoflow_webhooks:userid'] = 'The ID of the user who created the webhook.';

$string['privacy:metadata:webhook'] = 'This plugin sends user data to external webhook services.';
$string['privacy:metadata:webhook:action'] = 'Action that triggered the event.';
$string['privacy:metadata:webhook:anonymous'] = 'Whether the event was anonymized.';
$string['privacy:metadata:webhook:city'] = 'City information of the user';
$string['privacy:metadata:webhook:component'] = 'Component generating the event.';
$string['privacy:metadata:webhook:contextid'] = 'Context ID associated with the event.';
$string['privacy:metadata:webhook:contextinstanceid'] = 'Instance ID of the event context.';
$string['privacy:metadata:webhook:country'] = 'Country of the user';
$string['privacy:metadata:webhook:courseid'] = 'ID of the related course.';
$string['privacy:metadata:webhook:courses'] = 'The courses in which the user is enrolled.';
$string['privacy:metadata:webhook:crud'] = 'CRUD operation type.';
$string['privacy:metadata:webhook:edulevel'] = 'Education level of the event.';
$string['privacy:metadata:webhook:email'] = 'The user\'s email address';
$string['privacy:metadata:webhook:event'] = 'The event name (short).';
$string['privacy:metadata:webhook:eventname'] = 'The fully-qualified event name dispatched from Moodle.';
$string['privacy:metadata:webhook:firstname'] = 'The firstname of the user.';
$string['privacy:metadata:webhook:groups'] = 'The groups the user is assigned to within courses.';
$string['privacy:metadata:webhook:lastname'] = 'The lastname of the user';
$string['privacy:metadata:webhook:objectid'] = 'Object ID involved in the event.';
$string['privacy:metadata:webhook:objecttable'] = 'Object table related to the event.';
$string['privacy:metadata:webhook:other'] = 'Additional event metadata.';
$string['privacy:metadata:webhook:profilefields'] = 'The user’s additional profile fields.';
$string['privacy:metadata:webhook:relateduserid'] = 'Related user in the event.';
$string['privacy:metadata:webhook:roles'] = 'The roles the user holds within courses.';
$string['privacy:metadata:webhook:target'] = 'Target object of the event.';
$string['privacy:metadata:webhook:timecreated'] = 'The time when the event happened.';
$string['privacy:metadata:webhook:userid'] = 'User ID related to the event.';
$string['privacy:metadata:webhook:username'] = 'The user\'s username.';
