<?php
// This file is part of the Zoom plugin for Moodle - http://moodle.org/
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
 * Load zoom meeting and assign grade to the user join the meeting.
 *
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 *
 * @package    mod_recordingszoom
 * @copyright  2018 Alejandro Escobar <ealejandro101@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once($CFG->libdir . '/gradelib.php');
require_once($CFG->libdir . '/moodlelib.php');
require_once(dirname(__FILE__).'/locallib.php');

// Course_module ID.
$id = required_param('id', PARAM_INT);
$zoom_id = required_param('zoom_id', PARAM_INT);
if ($id) {
    $cm         = get_coursemodule_from_id('recordingszoom', $id, 0, false, MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $recordingszoom  = $DB->get_record('recordingszoom', array('id' => $cm->instance), '*', MUST_EXIST);
} else {
    print_error('You must specify a course_module ID');
}

require_login($course, true, $cm);

$context = context_module::instance($cm->id);
$PAGE->set_context($context);

$zoomplayredirect = required_param('zoomplayredirect', PARAM_URL); // zoom play redirect.
$zoomstarttime = required_param('zoomstarttime', PARAM_TEXT); // zoom start time.


require_capability('mod/recordingszoom:view', $context);

if( !(($zoom_id == $recordingszoom->zoom_meeting_id) || ($zoom_id == $recordingszoom->zoom_meeting_id_2) || 
    ($zoom_id == $recordingszoom->zoom_meeting_id_3) || ($zoom_id == $recordingszoom->zoom_meeting_id_4) ) ){
        throw new moodle_exception('error', 'mod_recordingszoom', '', 'errorzoomidnovalido');
    }

// Check whether user had a grade. If no, then assign full credits to him or her.
$gradelist = grade_get_grades($course->id, 'mod', 'recordingszoom', $cm->instance, $USER->id);

// Assign full credits for user who has no grade yet, if this meeting is gradable
// (i.e. the grade type is not "None").
if (!empty($gradelist->items) && empty($gradelist->items[0]->grades[$USER->id]->grade)) {
    $grademax = $gradelist->items[0]->grademax;
    $grades = array('rawgrade' => $grademax,
                    'userid' => $USER->id,
                    'usermodified' => $USER->id,
                    'dategraded' => '',
                    'feedbackformat' => '',
                    'feedback' => '');

    recordingszoom_grade_item_update($recordingszoom, $grades);
}

// Redirect user to play zoom meeting.
$joinurl = new moodle_url($zoomplayredirect);

// Record user's clicking view recording.
\mod_recordingszoom\event\view_recording_button_clicked::create(array('context' => $context, 'objectid' => $zoom_id, 'other' =>
        array('cmid' => $id, 'meetingid' => (int) $zoom_id, 'zoomstarttime' => (string) $zoomstarttime )))->trigger();

redirect($joinurl);