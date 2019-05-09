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
 * Defines the view event.
 *
 * @package    mod_recordingszoom
 * @copyright  2018 Alejandro Escobar <ealejandro101@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_recordingszoom\event;

defined('MOODLE_INTERNAL') || die();

/**
 * Records when a join meeting button is clicked.
 *
 * @package    mod_recordingszoom
 * @copyright  2018 Alejandro Escobar <ealejandro101@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class view_recording_button_clicked extends \core\event\base {

    /**
     * Initializes the event.
     */
    protected function init() {
        $this->data['edulevel'] = self::LEVEL_PARTICIPATING;
        $this->data['crud'] = 'r';
        $this->data['objecttable'] = 'recordingszoom';
    }

    /**
     * Validates arguments.
     */
    protected function validate_data() {
        $fieldstovalidate = array('cmid' => "integer", 'meetingid' => "integer", "zoomstarttime" => ("string"));
        foreach ($fieldstovalidate as $field => $shouldbe) {
            if (is_null($this->other[$field])) {
                throw new \coding_exception("The $field value must be set in other.");
            } else if (gettype($this->other[$field]) != $shouldbe) {
                throw new \coding_exception("The $field value must be an $shouldbe." . " is " . gettype($this->other[$field]));
            }
        }
    }

    /**
     * Returns the name of the event.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('clickviewrecording', 'mod_recordingszoom');
    }

    /**
     * Returns a short description for the event.
     *
     * @return string
     */
    public function get_description() {
        return "User $this->userid View recording with meeting_id " .
                $this->other['meetingid'] . " and start time " . $this->other['zoomstarttime'] . " in course $this->courseid";
    }

    /**
     * Returns URL to meeting view page.
     *
     * @return moodle_url
     */
    public function get_url() {
        return new \moodle_url('/mod/recordingszoom/view.php', array('id' => $this->other['cmid']));
    }
}
