<?php
// This file is an extension of Moodle - http://moodle.org/
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
 * SIS API enrolment plugin.
 *
 * This plugin synchronises enrolment and roles with external sisapi table.
 *
 * @package    enrol
 * @subpackage sisapi
 * @copyright  2012 Jason Cameron {@link http://jbkc85.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @remarks I am using the external database enrol code as a template, so thanks
 * to Petr Skoda et al. for the example coding!
 */

defined('MOODLE_INTERNAL') || die();

/**
 * SIS API enrolment plugin implementation.
 * @author  Jason cameron - based on code by Petr Skoda et al
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class enrol_sisapi_plugin extends enrol_plugin {
    /**
     * @param object $instance
     * @return bool
     */
    public function instance_deleteable( $instance ){
        if (!enrol_is_enabled('sisapi')){
            return true;
        }
	$check_configs = array('apitype','apiurl','remoteenroltable','remotecoursefield','remoteuserfield');
	if($this->check_required_config( $check_configs )){ 
            return true;
        }

        return false;
    }

    /**
     */
    public function allow_unenrol_user( stdClass $instance, stdClass $ue ){
        if ($ue->status == ENROL_USER_SUSPENDED){
            return true;
        }

        return false;
    }

    /**
     */
    public function get_user_enrolment_actions( course_enrolment_manager $manager, $ue){
        $actions = array();
        $context = $manager->get_context();
        $instance = $ue->enrolmentinstance;
        $params = $manager->get_moodlepage()->url->params();
        $params['ue'] = $ue->id;
        if( $this->allow_unenrol_user($instance, $ue) && has_capability('enrol/sisapi:unenrol', $context)){
            $url = new moodle_url('/enrol/unenroluser.php', $params);
            $actions[] = new user_enrolment_action(new pix_icon('t/delete',''),get_string('unenrol', 'enrol'), $url, array('class'=>'unenrollink', 'rel'=>$ue->id));
        }
        return $actions;
    }

    /**
     * Forces sync of the User enrollments in the system with external sisapi
     * - does not create new courses.
     *
     * @param object $user user record
     * @return void
     */
    public function sync_user_enrolments( $user ){
        global $CFG, $DB;

        $check_configs = array('apitype','apikey','apiurl','remoteenroltable','remotecoursefield','remoteuserfield');
        if(!$this->check_required_config( $check_configs )){
            return;
        }

	$library = strtolower($this->get_config('apitype'));
	// Use SISLIB Local to module unless otherwise configured...
        //require_once("$CFG->dirroot/local/sislib/$library.class.php");
        require_once("$CFG->dirroot/enrol/sisapi/sislib/$library.class.php");

        switch( $library ){
            case "clever":
                $api = new SisAPI( $this->get_config('apikey'),
                    $this->get_config('apiurl'));
                break;
            case "learnsprout":
                $term = $this->get_config('termid');
                if( !empty($term) ){ 
                    $api = new SisAPI( $this->get_config('apikey'),
                        $this->get_config('apiurl'),
                        $this->get_config('orgid'),
                        $this->get_config('termid'));
                } else {
                    $api = new SisAPI( $this->get_config('apikey'),
                        $this->get_config('apiurl'),
                        $this->get_config('orgid'));
                }
                break;
            default:
                $api = new SisAPI( $this->get_config('apikey'),
                    $this->get_config('apiurl'),
                    $this->get_config('apiuser')); 
	}
/** Moved inside of switch case
        $api = new SisApi($this->get_config('apikey'),
                          $this->get_config('apiurl'),
                          $this->get_config('apiuser'));
 */

        $cache = $this->get_config('localcaching');
        if( $cache == 'none' ){}
        else if( $user->lastlogin < strtotime("{$cache}")){
            if( debugging() ){ 
                error_log("Time to Cache: $cache - Changed to "
                          .strtotime("{$cache}")); 
            }
            return;
        }

	$localuserfield = $this->get_config('localuserfield');
	if( $user->$localuserfield == '' ){
		return false;
	}

        // TODO: Wrap logic in statement to query for teacher if student doesn't
        // return a valid student object
        /**
	Uncomment to see the query parameters for the API call
	if( debugging() ){
            debugging('Query params:'.$api->formQuery($localuserfield,$user));
	}
        */
        $userType = 'student';
	//$apiuser =$api->getUser($api->formQuery($localuserfield,$user),$type);
        $apiuser = $api->getUser( $user,$localuserfield,$userType );

        if( $library == 'learnsprout' ){
            $apiuserdata = $apiuser;
        } else {
            $apiuserdata = $apiuser->data[0];
        }

        if( empty($apiuserdata) ){
            $userType = 'teacher';
            //$apiuser =$api->getUser($api->formQuery($localuserfield,$user),
            //    $type);
            $apiuser = $api->getUser( $user,$localuserfield,$userType );
            if( $library == 'learnsprout' ){
                $apiuserdata = $apiuser;
            } else {
                $apiuserdata = $apiuser->data[0];
            }
        }

        if( !empty($apiuserdata) ){
        if( $library == 'learnsprout' ){
            $apiuserid = $apiuser['id'];
        } else {
            $apiuserid = $apiuser->data[0]->data->id;
        }
        $apicoursecall = $api->getAssociatedObjects( $apiuserid, 'sections', "{$userType}" );
/**
        if( debugging() ){
            debugging('Found '.count($apicoursecall).' API Courses');
        }
*/

        if( $library == 'learnsprout' ){
            $apicourses = $apicoursecall;
        } else {
            $apicourses = $apicoursecall->data;
        }

	// Get Default Config Variables
	$localrolefield = $this->get_config('localrolefield');
	$localcoursefield = $this->get_config('localcoursefield');
	$defaultrole  = $this->get_config('defaultrole');
	$ignorehidden = $this->get_config('ignorehiddencourses');

        // Check the user variable to ensure it is a valid object
        if (!is_object($user) or !property_exists($user, 'id')){
            throw new coding_exception('Invalid $user parameter in sync_user_enrolments()');
        }

	// Check properties
        if ( $localuserfield == "firstandlast" ){
            if ( !property_exists($user, "firstname") ||
                 !property_exists($user, "lastname")){
                debugging('Invalid $user parameter in sync_user_enrolments(), missing '.$localuserfield);
                $user = $DB->get_record('user', array('id'=>$user->id));
            }
        } else {
	    if (!property_exists($user, $localuserfield)){
                debugging('Invalid $user parameter in sync_user_enrolments(), missing '.$localuserfield);
                $user = $DB->get_record('user', array('id'=>$user->id));
            }
        }

	// create roles mapping
	$allroles = get_all_roles();
        if (!isset($allroles[$defaultrole])){
            $defaultrole = 0;
        }
        $roles = array();
        foreach( $allroles as $role ){
            $roles[$role->$localrolefield] = $role->id;
        }

        $enrols = array();
        $instances = array();

        // Read Remote Enrolls
        foreach($apicourses as $apicourse){
            // Need to set specific parameters to look for per API call
            switch( $library ){
                case "clever":
                    $course_sis_id = $apicourse->data->sis_id;
                    $teacherapiid = $apicourse->data->teacher;
                    break;
                case "learnsprout":
                    $courseapiid = $api->getCourse($apicourse['course']['id']);
                    $course_sis_id = $courseapiid['number'];
                    $teacherapiid = $apicourse['teacher']['id'];
                    break;
                default:
            }
            //error_log( "SIS_ID: $course_sis_id" );
            //JC - Switched 'course_number' to 'sis_id' Dec 31, 2012 due to dup
            //course numbers in some SIS
            if (!$course = $DB->get_record('course', array($localcoursefield=>$course_sis_id), 'id,visible')){
                continue;
            }
            if (!$course->visible and $ignorehidden){
                continue;
            }

            if ( $apiuserid != $teacherapiid ){
                // Assign Default role
                if (!$defaultrole){
                    continue;
                }
                $roleid = $defaultrole;
            } else {
                // Assign Teacher role
                $roleid = $roles['editingteacher'];
            }
            $enrols[$course->id][] = $roleid;
      
            if ($instance = $DB->get_record('enrol', array('courseid'=>$course->id, 'enrol'=>'sisapi'), '*', IGNORE_MULTIPLE)){
                $instances[$course->id] = $instance;
                continue;
            }
          
            $enrolid = $this->add_instance($course);
            $instances[$course->id] = $DB->get_record('enrol', array('id'=>$enrolid));
        }

        // Enroll user into courses and sync roles
        foreach ($enrols as $courseid => $roles) {
            if (!isset($instances[$courseid])) {
                // ignored
                continue;
            }
            $instance = $instances[$courseid];

            if ($e = $DB->get_record('user_enrolments', array('userid'=>$user->id, 'enrolid'=>$instance->id))) {
                // reenable enrolment when previously disable enrolment refreshed
                if ($e->status == ENROL_USER_SUSPENDED) {
                    $this->update_user_enrol($instance, $user->id, ENROL_USER_ACTIVE);
                }
            } else {
                $roleid = reset($roles);
                $this->enrol_user($instance, $user->id, $roleid, 0, 0, ENROL_USER_ACTIVE);
            }

            if (!$context = get_context_instance(CONTEXT_COURSE, $instance->courseid)) {
                //weird
                continue;
            }
            $current = $DB->get_records('role_assignments', array('contextid'=>$context->id, 'userid'=>$user->id, 'component'=>'enrol_sisapi', 'itemid'=>$instance->id), '', 'id, roleid');

            $existing = array();
            foreach ($current as $r) {
                if (in_array($r->roleid, $roles)) {
                    $existing[$r->roleid] = $r->roleid;
                } else {
                    role_unassign($r->roleid, $user->id, $context->id, 'enrol_sisapi', $instance->id);
                }
            }
            foreach ($roles as $rid) {
                if (!isset($existing[$rid])) {
                    role_assign($rid, $user->id, $context->id, 'enrol_sisapi', $instance->id);
                }
            }
        }

        // unenrol as necessary
        $sql = "SELECT e.*, c.visible AS cvisible, ue.status AS ustatus
                  FROM {enrol} e
                  JOIN {user_enrolments} ue ON ue.enrolid = e.id
                  JOIN {course} c ON c.id = e.courseid
                 WHERE ue.userid = :userid AND e.enrol = 'sisapi'";
        $rs = $DB->get_recordset_sql($sql, array('userid'=>$user->id));
        foreach ($rs as $instance) {
            if (!$instance->cvisible and $ignorehidden) {
                continue;
            }

            if (!$context = get_context_instance(CONTEXT_COURSE, $instance->courseid)) {
                //weird
                continue;
            }

            if (!empty($enrols[$instance->courseid])) {
                // we want this user enrolled
                continue;
            }

            // deal with enrolments removed from external table
            if ($unenrolaction == ENROL_EXT_REMOVED_UNENROL) {
                // unenrol
                $this->unenrol_user($instance, $user->id);

            } else if ($unenrolaction == ENROL_EXT_REMOVED_KEEP) {
                // keep - only adding enrolments

            } else if ($unenrolaction == ENROL_EXT_REMOVED_SUSPEND or $unenrolaction == ENROL_EXT_REMOVED_SUSPENDNOROLES) {
                // disable
                if ($instance->ustatus != ENROL_USER_SUSPENDED) {
                    $this->update_user_enrol($instance, $user->id, ENROL_USER_SUSPENDED);
                }
                if ($unenrolaction == ENROL_EXT_REMOVED_SUSPENDNOROLES) {
                    role_unassign_all(array('contextid'=>$context->id, 'userid'=>$user->id, 'component'=>'enrol_sisapi', 'itemid'=>$instance->id));
                }
            }
        }
        }
    }
        
    private function check_required_config( $configvars ){
        foreach( $configvars as $var ){
            if(!$this->get_config($var)) return true;
        }
	return true;
    }
}

