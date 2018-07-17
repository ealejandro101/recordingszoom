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


$strpagetitle = 'Lista de grabaciones vinculadas con reunión de Zoom';
$strtopic = 'Tema';
$strstarttime = 'Fecha de inicio';
$strduration =  'Duracion';
$strplayurl = 'Acción';
$strtitulodelalista = 'Lista de grabaciones para la reunión ' . $recordingszoom->zoom_meeting_id;
$strplayrecording = 'Ver grabación';
// Print the page header.
$PAGE->set_url('/mod/recordingszoom/view.php', array('id' => $cm->id));
$PAGE->set_title( $strpagetitle );
$PAGE->set_heading(format_string($course->fullname));

$PAGE->set_cacheable(false);
/*
 * Other things you may want to set - remove if not needed.
 * $PAGE->set_focuscontrol('some-html-id');
 * $PAGE->add_body_class('recordingszoom-'.$somevar);
 */


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


// Output starts here.

echo $OUTPUT->header();

// Conditions to show the intro can change to look for own settings or whatever.
if ($recordingszoom->intro) {
    echo $OUTPUT->box(format_module_intro('recordingszoom', $recordingszoom, $cm->id), 'generalbox mod_introbox', 'recordingszoomintro');
}

echo $OUTPUT->heading(format_string( $strtitulodelalista ), 2);

$table = new html_table();
$table->attributes['class'] = 'generaltable mod_view';

$table->align = array('center', 'left');
$numcolumns = 4;

$topic = new html_table_cell( $strtopic );
$topic->header = true;

$start_time = new html_table_cell($strstarttime);
$start_time->header = true;

$duration = new html_table_cell( $strduration );
$duration->header = true;

$play_url = new html_table_cell( $strplayurl);
$play_url->header = true;

$table->data[] = array($topic, $start_time, $duration, $play_url );

foreach ($zoomlistmeetings_with_recordings as $meeting_recording ) {

    $topic = new html_table_cell($meeting_recording->topic);
    $start_time = new html_table_cell($meeting_recording->start_time);
    $duration = new html_table_cell($meeting_recording->duration);
    // Tabla interior con lista de botones para ver grabación
    $table_url_file_recording_mp4 = new html_table();
    // Todo, revisar el estilo de la subtabla $table_url_file_recording_mp4->attributes['class'] = 'generaltable';
    $table_url_file_recording_mp4->align = array('center', 'left');
    
    foreach($meeting_recording->recording_files as $file_recording){
        if($file_recording->file_type == "MP4"){
            $link = html_writer::link( $file_recording->play_url, $strplayrecording);
            //$link = 'Ver grabación '. $file_recording->play_url;
            $cell_play_url_button  = new html_table_cell($link);
            $table_url_file_recording_mp4->data[] =  array($cell_play_url_button);
        }
    }
    // Todo, que hacer si no hay MP4?
    $play_url = new html_table_cell( html_writer::table($table_url_file_recording_mp4) );

    $table->data[] = array($topic, $start_time, $duration, $play_url );
}


echo html_writer::table($table);


// Finish the page.
echo $OUTPUT->footer();
