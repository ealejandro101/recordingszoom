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
 * Internal library of functions for module recordingszoom
 *
 * All the recordingszoom specific functions, needed to implement the module
 * logic, should go here. Never include this file from your lib.php!
 *
 * @package    mod_recordingszoom
 * @copyright  2018 Alejandro Escobar <ealejandro101@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot.'/mod/zoom/lib.php');

if (file_exists(dirname(__FILE__).'/vendor/firebase/php-jwt/src/JWT.php')) {
    require_once(dirname(__FILE__).'/vendor/firebase/php-jwt/src/JWT.php');
}


/**
 * Get course/cm/zoom objects from url parameters, and check for login/permissions.
 *
 * @return array Array of ($course, $cm, $zoom)
 */
function recordingszoom_get_instance_setup() {
    global $DB;

    $id = optional_param('id', 0, PARAM_INT); // Course_module ID, or
    $n  = optional_param('n', 0, PARAM_INT);  // ... recordingszoom instance ID - it should be named as the first character of the module.
    
    if ($id) {
        $cm         = get_coursemodule_from_id('recordingszoom', $id, 0, false, MUST_EXIST);
        $course     = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
        $recordingszoom  = $DB->get_record('recordingszoom', array('id' => $cm->instance), '*', MUST_EXIST);
    } else if ($n) {
        $recordingszoom  = $DB->get_record('recordingszoom', array('id' => $n), '*', MUST_EXIST);
        $course     = $DB->get_record('course', array('id' => $recordingszoom->course), '*', MUST_EXIST);
        $cm         = get_coursemodule_from_instance('recordingszoom', $recordingszoom->id, $course->id, false, MUST_EXIST);
    } else {
        error('You must specify a course_module ID or an instance ID');
    }
    
    require_login($course, true, $cm);

    $context = context_module::instance($cm->id);
    require_capability('mod/recordingszoom:view', $context);

    return array($course, $cm, $recordingszoom);
}

