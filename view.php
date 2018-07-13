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
 * Prints a particular instance of recordingszoom
 *
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 *
 * @package    mod_recordingszoom
 * @copyright  2018 Alejandro Escobar <ealejandro101@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(__FILE__).'/locallib.php');
require_once(dirname(__FILE__).'/zoom_broker.php');

list($course, $cm, $recordingszoom) = recordingszoom_get_instance_setup();

$event = \mod_recordingszoom\event\course_module_viewed::create(array(
    'objectid' => $PAGE->cm->instance,
    'context' => $PAGE->context,
));
$event->add_record_snapshot('course', $PAGE->course);
$event->add_record_snapshot($PAGE->cm->modname, $recordingszoom);
$event->trigger();

// Print the page header.

$PAGE->set_url('/mod/recordingszoom/view.php', array('id' => $cm->id));
$PAGE->set_title(format_string($recordingszoom->name));
$PAGE->set_heading(format_string($course->fullname));


$PAGE->set_cacheable(false);
/*
 * Other things you may want to set - remove if not needed.
 * $PAGE->set_focuscontrol('some-html-id');
 * $PAGE->add_body_class('recordingszoom-'.$somevar);
 */

// Output starts here.
echo $OUTPUT->header();

// Conditions to show the intro can change to look for own settings or whatever.
if ($recordingszoom->intro) {
    echo $OUTPUT->box(format_module_intro('recordingszoom', $recordingszoom, $cm->id), 'generalbox mod_introbox', 'recordingszoomintro');
}

// Retrieve a meeting information with zoom v2 API
$zoommeeting = mod_recordingszoom_get_meeting_info($recordingszoom);



$host_id = $zoommeeting->host_id;

var_dump($host_id);

/** 
 * ToDo - Validación que el host_id este matriculado como profesor del curso
 * Consultar el email_zoom del usuario con el host_id
 * Buscar en los profesores del curso el email_zoom
 * */

// Retrieve List all the recordings with zoom v2 API
$zoomlistmeetings_with_recordings =  mod_recordingszoom_get_user_cloudrecordings_list($recordingszoom, $host_id );

var_dump($zoomlistmeetings_with_recordings);

$table = new html_table();
$table->attributes['class'] = 'generaltable mod_view';

$table->align = array('center', 'left');
$numcolumns = 5;









// Finish the page.
echo $OUTPUT->footer();
