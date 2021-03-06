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
 * The main recordingszoom configuration form
 *
 * It uses the standard core Moodle formslib. For more info about them, please
 * visit: http://docs.moodle.org/en/Development:lib/formslib.php
 *
 * @package    mod_recordingszoom
 * @copyright  2018 Alejandro Escobar <ealejandro101@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/course/moodleform_mod.php');

/**
 * Module instance settings form
 *
 * @package    mod_recordingszoom
 * @copyright  2018 Alejandro Escobar <ealejandro101@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_recordingszoom_mod_form extends moodleform_mod {

    /**
     * Defines forms elements
     */
    public function definition() {
        global $CFG;
        global $USER;
        
        $mform = $this->_form;

        // Adding the "general" fieldset, where all the common settings are showed.
        $mform->addElement('header', 'general', get_string('general', 'form'));

        // Adding the standard "name" field, meeting zoom topic.
        $mform->addElement('text', 'name', get_string('recordingszoomname', 'mod_recordingszoom'), array('size' => '64'));
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEANHTML);
        }
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        $mform->addHelpButton('name', 'recordingszoomname', 'recordingszoom');

        // Adding the standard "intro" and "introformat" fields.
        if ($CFG->branch >= 29) {
            $this->standard_intro_elements();
        } else {
            $this->add_intro_editor();
        }

        $ismanager = true;
        if (!empty($this->_cm)) {
            $context = context_module::instance($this->_cm->id);
            $ismanager = has_capability('mod/recordingszoom:edit', $context);
        }
        
        if($ismanager){
            // Adicionar el zoom id
            $mform->addElement('text', 'zoom_meeting_id', get_string('zoommeetingid', 'recordingszoom'), array('size' == '10'));
            $mform->setType('zoom_meeting_id', PARAM_INT);
            $mform->addRule('zoom_meeting_id', get_string('falla_zoommeetingid', 'recordingszoom'), 'required', null, 'client');
            $mform->addHelpButton('zoom_meeting_id', 'zoommeetingid', 'recordingszoom');

            // Adicionar el zoom id 2
            $mform->addElement('text', 'zoom_meeting_id_2', get_string('zoommeetingid', 'recordingszoom'), array('size' == '10'));
            $mform->setType('zoom_meeting_id_2', PARAM_INT);
            //$mform->addRule('zoom_meeting_id_2', get_string('falla_zoommeetingid', 'recordingszoom'), 'required', null, 'client');
            $mform->addHelpButton('zoom_meeting_id_2', 'zoommeetingid', 'recordingszoom');

            // Adicionar el zoom id 3
            $mform->addElement('text', 'zoom_meeting_id_3', get_string('zoommeetingid', 'recordingszoom'), array('size' == '10'));
            $mform->setType('zoom_meeting_id_3', PARAM_INT);
            //$mform->addRule('zoom_meeting_id_3', get_string('falla_zoommeetingid', 'recordingszoom'), 'required', null, 'client');
            $mform->addHelpButton('zoom_meeting_id_3', 'zoommeetingid', 'recordingszoom');

            // Adicionar el zoom id 4
            $mform->addElement('text', 'zoom_meeting_id_4', get_string('zoommeetingid', 'recordingszoom'), array('size' == '10'));
            $mform->setType('zoom_meeting_id_4', PARAM_INT);
            //$mform->addRule('zoom_meeting_id_4', get_string('falla_zoommeetingid', 'recordingszoom'), 'required', null, 'client');
            $mform->addHelpButton('zoom_meeting_id_4', 'zoommeetingid', 'recordingszoom');

        } else {
            $mform->addElement('static', 'zoom_meeting_id', get_string('zoommeetingid', 'recordingszoom'), get_string('zoommeetingid_desc', 'recordingszoom'));
            $mform->addElement('static', 'zoom_meeting_id_2', get_string('zoommeetingid', 'recordingszoom'), get_string('zoommeetingid_desc', 'recordingszoom'));
            $mform->addElement('static', 'zoom_meeting_id_3', get_string('zoommeetingid', 'recordingszoom'), get_string('zoommeetingid_desc', 'recordingszoom'));
            $mform->addElement('static', 'zoom_meeting_id_4', get_string('zoommeetingid', 'recordingszoom'), get_string('zoommeetingid_desc', 'recordingszoom'));
        }

        // Add standard grading elements.
        $this->standard_grading_coursemodule_elements();
        $mform->setDefault('grade', false);

        // Add standard elements, common to all modules.
        $this->standard_coursemodule_elements();

        // Add standard buttons, common to all modules.
        $this->add_action_buttons();
        
    }
}

/**
 * Form to search for meeting reports.
 *
 * @package    mod_zoom
 * @copyright  2015 UC Regents
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_zoom_report_form extends moodleform {
    /**
     * Define form elements.
     */
    public function definition() {
        $mform = $this->_form;

        $mform->addElement('date_selector', 'from', get_string('from'));

        $mform->addElement('date_selector', 'to', get_string('to'));

        $mform->addElement('submit', 'submit', get_string('go'));
    }
}