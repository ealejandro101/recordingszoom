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
require_once(dirname(__FILE__).'/mod_form.php');


list($course, $cm, $recordingszoom) = recordingszoom_get_instance_setup();

$event = \mod_recordingszoom\event\course_module_viewed::create(array(
    'objectid' => $PAGE->cm->instance,
    'context' => $PAGE->context,
));
$event->add_record_snapshot('course', $PAGE->course);
$event->add_record_snapshot($PAGE->cm->modname, $recordingszoom);
$event->trigger();

/**
 * Cadenas utilizadas para el idioma
 */
$strpagetitle = get_string('pagetitle', 'mod_recordingszoom');
$strtopic = get_string('topic', 'mod_recordingszoom');
$strstarttime =  get_string('starttime', 'mod_recordingszoom');
$strduration =  get_string('duration', 'mod_recordingszoom');
$straccion = get_string('accion', 'mod_recordingszoom');
$strtitulodelalista =  get_string('titulodelalista', 'mod_recordingszoom') . $recordingszoom->name . ' - ' . $recordingszoom->zoom_meeting_id;
$strplayrecording = get_string('playrecording', 'mod_recordingszoom');
$strerr_long_timeframe = get_string('err_long_timeframe', 'mod_recordingszoom');

/**
 * Print the page header.
 * 
 */
$PAGE->set_url('/mod/recordingszoom/recordinglist.php', array('id' => $cm->id));
$PAGE->set_title( $strpagetitle );
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_cacheable(false);
/*
 * Other things you may want to set - remove if not needed.
 * $PAGE->set_focuscontrol('some-html-id');
 * $PAGE->add_body_class('recordingszoom-'.$somevar);
 */


 /**
  * Obteniendo los datos pasados por GET en la URL o defecto de busqueda
  */

$date_past = strtotime('-30 day');
$date_past = getdate($date_past);
$date_past['month'] = $date_past['mon'];
$date_past['day'] = $date_past['mday'];

$now = getdate();
$now['month'] = $now['mon'];
$now['day'] = $now['mday'];

$from = optional_param_array('from', $date_past, PARAM_INT);
$to = optional_param_array('to', $now, PARAM_INT);
$ffrom = sprintf('%u-%u-%u', $from['year'], $from['month'], $from['day']);
$fto = sprintf('%u-%u-%u', $to['year'], $to['month'], $to['day']);

/** 
 * ToDo - Validación que el host_id este matriculado como profesor del curso
 * Consultar el email_zoom del usuario con el host_id
 * Buscar en los profesores del curso el email_zoom
 * */
/**
 * Consulta de información utilizando las funciones en el broker
 */
// Retrieve List all the recordings with zoom v2 API
$zoomlistmeetings_with_recordings =  mod_recordingszoom_get_cloudrecordings_list($recordingszoom->zoom_meeting_id,  $ffrom, $fto );

// Output starts here.

echo $OUTPUT->header();
$currenttab = 'recordinglist';
require('tabs.php');

// Conditions to show the intro can change to look for own settings or whatever.
if ($recordingszoom->intro) {
    echo $OUTPUT->box(format_module_intro('recordingszoom', $recordingszoom, $cm->id), 'generalbox mod_introbox', 'recordingszoomintro');
}

echo $OUTPUT->heading(format_string( $strtitulodelalista ), 3);

if (!empty($zoomlistmeetings_with_recordings)) {

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

    $play_url = new html_table_cell( $straccion);
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

                $buttonhtml = html_writer::tag('button', $strplayrecording, array('type' => 'submit', 'class' => 'btn btn-primary'));
                $aurl = new moodle_url('/mod/recordingszoom/loadmeeting.php', array('id' => $cm->id, 'zoomplayredirect' => $file_recording->play_url));
                $buttonhtml .= html_writer::input_hidden_params($aurl);
                $link = html_writer::tag('form', $buttonhtml, array('action' => $aurl->out_omit_querystring()));

                $cell_play_url_button  = new html_table_cell($link);
                $table_url_file_recording_mp4->data[] =  array($cell_play_url_button);
            }
        }
        // Todo, que hacer si no hay MP4?
        $play_url = new html_table_cell( html_writer::table($table_url_file_recording_mp4) );

        $table->data[] = array($topic, $start_time, $duration, $play_url );
    }

}

$dateform = new mod_zoom_report_form('recordinglist.php?id='.$cm->id);
$dateform->set_data(array('from' => $from, 'to' => $to));
echo $dateform->render();

if (!empty($table->data)) {
    echo html_writer::table($table);
} else {
    echo $OUTPUT->notification(get_string('nosessions', 'mod_recordingszoom'), 'notifymessage');
}


// Finish the page.
echo $OUTPUT->footer();
