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
$PAGE->set_title( 'Lista de grabaciones vinculadas con reunión de Zoom');
$PAGE->set_heading(format_string($course->fullname));

$PAGE->set_cacheable(false);
/*
 * Other things you may want to set - remove if not needed.
 * $PAGE->set_focuscontrol('some-html-id');
 * $PAGE->add_body_class('recordingszoom-'.$somevar);
 */

// Output starts here.

$strtopic = get_string('topic', 'mod_recordingszoom');



echo $OUTPUT->header();

// Conditions to show the intro can change to look for own settings or whatever.
if ($recordingszoom->intro) {
    echo $OUTPUT->box(format_module_intro('recordingszoom', $recordingszoom, $cm->id), 'generalbox mod_introbox', 'recordingszoomintro');
}

// Retrieve a meeting information with zoom v2 API
$zoommeeting = mod_recordingszoom_get_meeting_info($recordingszoom);




$host_id = $zoommeeting->host_id;


/** 
 * ToDo - Validación que el host_id este matriculado como profesor del curso
 * Consultar el email_zoom del usuario con el host_id
 * Buscar en los profesores del curso el email_zoom
 * */

// Retrieve List all the recordings with zoom v2 API
$zoomlistmeetings_with_recordings =  mod_recordingszoom_get_user_cloudrecordings_list($recordingszoom, $host_id );

$table = new html_table();
$table->attributes['class'] = 'generaltable mod_view';

$table->align = array('center', 'left');
$numcolumns = 4;

$topic = new html_table_cell( $strtopic );
$topic->header = true;

$start_time = new html_table_cell(get_string('start_time', 'recordingszoom'));
$start_time->header = true;

$duration = new html_table_cell(get_string('duration', 'recordingszoom'));
$duration->header = true;

$play_url = new html_table_cell(get_string('play_url', 'recordingszoom'));
$play_url->header = true;

$table->data[] = array($topic, $start_time, $duration, $play_url );

foreach ($zoomlistmeetings_with_recordings as $meeting_recording ) {

    $topic = new html_table_cell($meeting_recording->topic);
    $start_time = new html_table_cell($meeting_recording->start_time);
    $duration = new html_table_cell($meeting_recording->duration);
    $url_file_recording_mp4 = "";
    foreach($meeting_recording->recording_files as $file_recording){
        if($file_recording->file_type == "MP4"){
            $url_file_recording_mp4 = $url_file_recording_mp4 . " - " . $file_recording->play_url;
        }
    }
    // Todo, que hacer si no hay MP4?
    $play_url = new html_table_cell($url_file_recording_mp4);

    $table->data[] = array($topic, $start_time, $duration, $play_url );
}


echo html_writer::table($table);


// Finish the page.
echo $OUTPUT->footer();
