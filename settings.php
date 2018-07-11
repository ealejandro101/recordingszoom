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
 * Settings.
 *
 * @package    mod_recordingszoom
 * @copyright  2018 Alejandro Escobar <ealejandro101@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {

    $settings = new admin_settingpage('modsettingrecordingszoom', get_string('pluginname', 'mod_recordingszoom'));

    $apiurl = new admin_setting_configtext('mod_recordingszoom/apiurl', get_string('apiurl', 'mod_recordingszoom'),
            get_string('apiurl_desc', 'mod_recordingszoom'), 'https://api.zoom.us/v2/', PARAM_URL);
    $settings->add($apiurl);

    $apikey = new admin_setting_configtext('mod_recordingszoom/apikey', get_string('apikey', 'mod_recordingszoom'),
            get_string('apikey_desc', 'mod_recordingszoom'), '', PARAM_ALPHANUMEXT);
    $settings->add($apikey);

    $apisecret = new admin_setting_configtext('mod_recordingszoom/apisecret', get_string('apisecret', 'mod_recordingszoom'),
            get_string('apisecret_desc', 'mod_recordingszoom'), '', PARAM_ALPHANUMEXT);
    $settings->add($apisecret);
}
