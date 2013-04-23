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
 * SIS API enrolment plugin settings and presets.
 *
 * @package    enrol
 * @subpackage sisapi
 * @copyright  2012 Jason Cameron {@link http://jbkc85.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {

    // General Settings
    $settings->add(new admin_setting_heading('enrol_sisapi_settings', '', get_string('pluginname_desc', 'enrol_sisapi')));

    $options = array("","LearnSprout","Clever");
    $options = array_combine($options, $options);
    $settings->add(new admin_setting_configselect('enrol_sisapi/apitype', get_string('apitype', 'enrol_sisapi'), get_string('apitype_desc', 'enrol_sisapi'), '', $options));

    $settings->add(new admin_setting_configtext('enrol_sisapi/apiurl', get_string('apiurl', 'enrol_sisapi'), get_string('apiurl_desc', 'enrol_sisapi'), ''));

    $settings->add(new admin_setting_configtext('enrol_sisapi/apiuser', get_string('apiuser', 'enrol_sisapi'), '', ''));

    $settings->add(new admin_setting_configpasswordunmask('enrol_sisapi/apikey', get_string('apikey', 'enrol_sisapi'), '', ''));

    $settings->add(new admin_setting_configtext('enrol_sisapi/orgid', get_string('orgid', 'enrol_sisapi'), get_string('orgid_desc', 'enrol_sisapi'), ''));

    $settings->add(new admin_setting_configcheckbox('enrol_sisapi/defaultssl', get_string('defaultssl', 'enrol_sisapi'), get_string('defaultssl_desc', 'enrol_sisapi'), 0));

    $settings->add(new admin_setting_heading('enrol_sisapi_localheader', get_string('settingsheaderlocal', 'enrol_sisapi'), ''));

    $options = array('id'=>'id', 'idnumber'=>'idnumber', 'shortname'=>'shortname');
    $settings->add(new admin_setting_configselect('enrol_sisapi/localcoursefield', get_string('localcoursefield', 'enrol_sisapi'), '', 'idnumber', $options));

    $options = array('idnumber'=>'idnumber', 'email'=>'email', 'username'=>'username', 'firstandlast'=>'firstandlast'); // only local users if username selected, no mnet users!
    $settings->add(new admin_setting_configselect('enrol_sisapi/localuserfield', get_string('localuserfield', 'enrol_sisapi'), '', 'idnumber', $options));

    $options = array('id'=>'id', 'shortname'=>'shortname', 'fullname'=>'fullname');
    $settings->add(new admin_setting_configselect('enrol_sisapi/localrolefield', get_string('localrolefield', 'enrol_sisapi'), '', 'shortname', $options));

    $options = array('id'=>'id', 'idnumber'=>'idnumber');
    $settings->add(new admin_setting_configselect('enrol_sisapi/localcategoryfield', get_string('localcategoryfield', 'enrol_sisapi'), '', 'id', $options));

    $caching = array('none'=>'none', '1 day'=>'+1 day', '5 days'=>'+5 days', 'week'=>'+1 week');    
    $settings->add(new admin_setting_configselect('enrol_sisapi/localcaching', get_string('localcaching','enrol_sisapi'), get_string('localcaching_desc','enrol_sisapi'), 'id', $caching));

    if (!during_initial_install()) {
        $options = get_default_enrol_roles(get_context_instance(CONTEXT_SYSTEM));
        $student = get_archetype_roles('student');
        $student = reset($student);
        $settings->add(new admin_setting_configselect('enrol_sisapi/defaultrole', get_string('defaultrole', 'enrol_sisapi'), get_string('defaultrole_desc', 'enrol_sisapi'), $student->id, $options));
    }

/** Also not needed currently
    $settings->add(new admin_setting_configcheckbox('enrol_sisapi/ignorehiddencourses', get_string('ignorehiddencourses', 'enrol_sisapi'), get_string('ignorehiddencourses_desc', 'enrol_sisapi'), 0));

    $options = array(ENROL_EXT_REMOVED_UNENROL        => get_string('extremovedunenrol', 'enrol'),
                     ENROL_EXT_REMOVED_KEEP           => get_string('extremovedkeep', 'enrol'),
                     ENROL_EXT_REMOVED_SUSPEND        => get_string('extremovedsuspend', 'enrol'),
                     ENROL_EXT_REMOVED_SUSPENDNOROLES => get_string('extremovedsuspendnoroles', 'enrol'));
    $settings->add(new admin_setting_configselect('enrol_sisapi/unenrolaction', get_string('extremovedaction', 'enrol'), get_string('extremovedaction_help', 'enrol'), ENROL_EXT_REMOVED_UNENROL, $options));
*/


/** Coming in second version of the module - Jason Cameron
    $settings->add(new admin_setting_heading('enrol_sisapi_newcoursesheader', get_string('settingsheadernewcourses', 'enrol_sisapi'), ''));

    $settings->add(new admin_setting_configtext('enrol_sisapi/newcoursetable', get_string('newcoursetable', 'enrol_sisapi'), get_string('newcoursetable_desc', 'enrol_sisapi'), ''));

    $settings->add(new admin_setting_configtext('enrol_sisapi/newcoursefullname', get_string('newcoursefullname', 'enrol_sisapi'), '', 'fullname'));

    $settings->add(new admin_setting_configtext('enrol_sisapi/newcourseshortname', get_string('newcourseshortname', 'enrol_sisapi'), '', 'shortname'));

    $settings->add(new admin_setting_configtext('enrol_sisapi/newcourseidnumber', get_string('newcourseidnumber', 'enrol_sisapi'), '', 'idnumber'));

    $settings->add(new admin_setting_configtext('enrol_sisapi/newcoursecategory', get_string('newcoursecategory', 'enrol_sisapi'), '', ''));

    if (!during_initial_install()) {
        require_once($CFG->dirroot.'/course/lib.php');
        $options = array();
        $parentlist = array();
        make_categories_list($options, $parentlist);
        $settings->add(new admin_setting_configselect('enrol_sisapi/defaultcategory', get_string('defaultcategory', 'enrol_sisapi'), get_string('defaultcategory_desc', 'enrol_sisapi'), 1, $options));
        unset($parentlist);
    }

    $settings->add(new admin_setting_configtext('enrol_sisapi/templatecourse', get_string('templatecourse', 'enrol_sisapi'), get_string('templatecourse_desc', 'enrol_sisapi'), ''));
**/
}
