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
 * Strings for component 'enrol_sisapi', language 'en'
 *
 * @package   enrol_sisapi
 * @copyright 2012 Jason Cameron {@link http://jbkc85.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['sisapi:unenrol'] = 'Unenrol suspended users';
$string['apiurl'] = 'API URL';
$string['apiurl_desc'] = 'URL to access the SIS API on an external server.';
$string['apikey'] = 'API Key';
$string['apitype'] = 'API Library';
$string['apitype_desc'] = 'API Library to use when utilizing the sisapi module.';
$string['apiuser'] = 'API User';
$string['orgid'] = 'Organization ID in the SIS Datastore.';
$string['orgid_desc'] = 'Organization ID, needed for LearnSprout Queries.';
$string['defaultssl'] = 'Enable SSL by Default (overwrites any HTTP in URL).';
$string['defaultssl_desc'] = 'Enable SSL by Default with the API call.';
$string['debugdb'] = 'Debug ADOdb';
$string['debugdb_desc'] = 'Debug ADOdb connection to external sisapi - use when getting empty page during login. Not suitable for production sites!';
$string['defaultcategory'] = 'Default new course category';
$string['defaultcategory_desc'] = 'The default category for auto-created courses. Used when no new category id specified or not found.';
$string['defaultrole'] = 'Default role';
$string['defaultrole_desc'] = 'The role that will be assigned by default if no other role is specified in external table.';
$string['ignorehiddencourses'] = 'Ignore hidden courses';
$string['ignorehiddencourses_desc'] = 'If enabled users will not be enrolled on courses that are set to be unavailable to students.';
$string['localcategoryfield'] = 'Local category field';
$string['localcoursefield'] = 'Local Course Field to Query against SISAPI';
$string['localrolefield'] = 'Local role field';
$string['localuserfield'] = 'Local User Field to Query against SISAPI';
$string['localcaching'] = 'Time between Enrollment Checks';
$string['localcaching_desc'] = "As SISAPI calls external functions, it may not be best for production environments to run check the SISAPI for changes everytime a user logs in.  This setting will allow you to base your checks on the 'lastlogin' user variable.";
$string['newcoursetable'] = 'Remote new courses table';
$string['newcoursetable_desc'] = 'Specify of the name of the table that contains list of courses that should be created automatically. Empty means no courses are created.';
$string['newcoursecategory'] = 'New course category field';
$string['newcoursefullname'] = 'New course full name field';
$string['newcourseidnumber'] = 'New course ID number field';
$string['newcourseshortname'] = 'New course short name field';
$string['pluginname'] = 'External sisapi';
$string['pluginname_desc'] = 'You can use an external sisapi (of nearly any kind) to control your enrolments. It is assumed your external sisapi contains at least a field containing a course ID, and a field containing a user ID. These are compared against fields that you choose in the local course and user tables.';
$string['remotecoursefield'] = 'Remote course field';
$string['remotecoursefield_desc'] = 'The name of the field in the remote table that we are using to match entries in the course table.';
$string['remoteenroltable'] = 'Remote user enrolment table';
$string['remoteenroltable_desc'] = 'Specify the name of the table that contains list of user enrolments. Empty means no user enrolment sync.';
$string['remoterolefield'] = 'Remote role field';
$string['remoterolefield_desc'] = 'The name of the field in the remote table that we are using to match entries in the roles table.';
$string['remoteuserfield'] = 'Remote user field';
$string['settingsheaderdb'] = 'External sisapi connection';
$string['settingsheaderlocal'] = 'Local field mapping';
$string['settingsheaderremote'] = 'Remote enrolment sync';
$string['settingsheadernewcourses'] = 'Creation of new courses';
$string['remoteuserfield_desc'] = 'The name of the field in the remote table that we are using to match entries in the user table.';
$string['templatecourse'] = 'New course template';
$string['templatecourse_desc'] = 'Optional: auto-created courses can copy their settings from a template course. Type here the shortname of the template course.';
