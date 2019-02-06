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

defined('MOODLE_INTERNAL') || die();

$toprow = array();
$toprow[] = new tabobject('meetinginfo', new moodle_url('/mod/recordingszoom/view.php', array('id' => $cm->id)), get_string('meetinginfo', 'mod_recordingszoom'));
$toprow[] = new tabobject('recordinglist', new moodle_url('/mod/recordingszoom/recordinglist.php', array('id' => $cm->id)), get_string('recordinglist', 'mod_recordingszoom'));

echo $OUTPUT->tabtree($toprow, $currenttab);
