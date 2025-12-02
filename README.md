# Zoho Flow Connector for Moodle

**Plugin type:** Local plugin  
**Component name:** `local_zohoflow`  
**Moodle versions supported:** 4.0 and above  

## Overview
The **Zoho Flow Connector for Moodle** plugin connects your Moodle site with **[Zoho Flow](https://www.zoho.com/flow/)**, a powerful no-code integration platform that lets you automate workflows across hundreds of applications — all without writing any code.

The plugin sends major Moodle events - including user creation, enrolment updates, course changes, completions, login activity, and grading events — as triggers to Zoho Flow. These triggers can instantly update CRMs, send notifications, sync data, or connect to any supported application.

It also enables inbound actions, allowing Zoho Flow to add or update users, manage enrolments, and retrieve user details in Moodle. With Zoho Flow’s drag-and-drop builder, logic controls, and extensive app ecosystem, this plugin adds a flexible no-code automation layer to your Moodle environment.

## Features

- Intuitive drag-and-drop flow builder
- Trigger from multiple sources, like app events, webhooks, schedules, emails, RSS/Atom feeds, and URLs
- Conditional logic using decisions, delays, and custom functions
- Deeper insights with dashboards and flow histories
- On-prem integrations to connect your apps with your on-premise system
- Team collaboration with Zoho Flow organizations
- Reduced margins of error with error branching and auto and manual reruns

## Installation

### Option 1: Install from ZIP file
1. Download the plugin ZIP file.
2. In your Moodle site, navigate to **Site administration → Plugins → Install plugins**.
3. Upload the ZIP file and click **Install plugin from ZIP file**.  
4. Complete the installation wizard.  

### Option 2: Manual installation
1. Extract the ZIP file.
2. Upload the folder `zohoflow` to:
<pre> /moodle/local/ </pre>
2. Log in as admin and go to **Site administration → Notifications** to complete the installation.

## Supported Trigger and Actions
| Triggers                                  | Description                                                   |
| ------------------------------------------| ------------------------------------------------------------- |
| `Course completed`                        | Triggers when a user completes a course                       |
| `Course created`                          | Triggers when a new course is created                         |
| `Course module completion state updated`  | Triggers when a user completed a course module                |
| `Course updated`                          | Triggers when a course is updated                             |
| `User created`                            | Triggers when a new user is added                             |
| `User enrolled`                           | Triggers when a user is enrolled in a course                  |
| `User enrolment removed`                  | Triggers when when a user's enrollment is removed from a course |
| `User enrolment updated`                  | Triggers when a user's enrollment is updated                  |
| `User graded`                             | Triggers when a user is graded in a selected course           |
| `User logged in`                          | Triggers when a user logs in                                  |
| `User logged out`                         | Triggers when a user logs out                                 |
| `User login failed`                       | Triggers when a user's login attempt fails                    |
| `User updated`                            | Triggers when an existing user profile is updated             |


| Actions                                   | Description                                                   |
| ----------------------------------------- | ------------------------------------------------------------- |
| `Add user`                                | Adds new user                                                 |
| `Enroll users to course`                  | Enrolls users into a selected course                          |
| `Fetch user`                              | Retrieves an existing user's details                          |
| `Unenroll user from course`               | Removes a user from a selected course                         |
| `Update user`                             | Updates an existing user’s details                            |

If you need additional triggers or actions, feel free to contact us at [support@zohoflow.com](support@zohoflow.com)

## Requirements
 - Moodle 4.x or higher.
 - PHP 7.3 or higher.
 - Admin permission.
 - Web services enabled.
 - REST protocol enabled

#### Enable web services
- Site administration → General → Advanced features → Enable web services

#### Enable REST protocal
- Site administration → Server → Manage protocols → Enable REST protocal

## How to Connect
### Create a Connection in Zoho Flow
You can create the Moodle connection in two ways:

### Method A — While creating a flow
1. Create a new flow in Zoho Flow.
2. Select any Moodle trigger or action.
3. Click **New connection** when prompted.

### Method B — From Zoho Flow Settings
1. Go to **Settings → Connections**.
2. Click **Create connection**.
3. Select the Moodle app.

In both methods, you will be asked for:
* Connection Name
* Moodle Base URL
* Token

### Get Your Token (from Moodle)
1. Go to **Site administration → Server → Manage tokens**.
2. Create a token for the “**Zoho Flow**” external service.
3. Copy the generated token and keep it ready.

### Find Your Moodle Base URL
Your Base URL is simply the main address of your Moodle site.

Example:
If your browser shows this page:
<pre> https://yourmoodlesite.com/admin/webservice/tokens.php </pre>
Then your Base URL is:
<pre> https://yourmoodlesite.com </pre>


## FAQ
### 1. What is Zoho Flow?

Zoho Flow is a powerful no-code/low-code integration platform that helps you connect hundreds of applications and automate workflows without writing any code. It provides a drag-and-drop builder, logic controls, and advanced automation tools.

### 2. What does this plugin do?

The plugin connects your Moodle site to Zoho Flow, allowing Moodle events (triggers) to start automated workflows. It also lets Zoho Flow perform Moodle actions like adding users, updating users, and managing enrolments.

### 3. Do I need coding experience to use this plugin?

No. All automations are created inside Zoho Flow using a no-code visual builder. Once installed, you can build workflows without writing any code.

### 4. What Moodle events can be automated?

You can automate workflows from events like course creation, updates, completions, user creation, enrolment changes, login activity, and grading events.

### 5. What Moodle actions can Zoho Flow perform?

Zoho Flow can add users, update user details, fetch user information, enrol users into courses, and unenroll them.

### 6. Does this plugin send data outside Moodle?

Yes — but only when a flow is configured to do so. Moodle events are securely sent to Zoho Flow when they are used as triggers in your flows.

### 7. Is Zoho Flow free to use?

Zoho Flow offers both free and paid plans. You can check available plans on Zoho Flow’s [official website](http://zohoflow.com/).

## License
This plugin is licensed under the [GNU General Public License](https://www.gnu.org/licenses/gpl-3.0.html).

## Author
[Zoho Flow](https://www.zoho.com/flow/)

