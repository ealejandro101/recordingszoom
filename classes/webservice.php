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
 * Defines the version and other meta-info about the plugin
 *
 * Setting the $plugin->version to 0 prevents the plugin from being installed.
 * See https://docs.moodle.org/dev/version.php for more info.
 *
 * @package    mod_recordingszoom
 * @copyright  2018 Alejandro Escobar <ealejandro101@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/mod/recordingszoom/firebase/JWT.php');

/**
 * Web service class.
 *
 * @package    mod_recordingszoom
 * @copyright  2018 Alejandro Escobar <ealejandro101@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_recordingszoom_webservice {


    public function get_meeting_info($recordingszoom) {

        
        




    }


    public function getUsers () {
        //list users endpoint GET https://api.zoom.us/v2/users
        $ch = curl_init('https://api.zoom.us/v2/users');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      
        // add token to the authorization header
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization: Bearer ' . generateJWT()
        ));
        $response = curl_exec($ch);
        $response = json_decode($response);
        return $response;
    }


    //function to generate JWT
    public function generateJWT () {
        // JWT PHP Library https://github.com/firebase/php-jwt
        
        $config = get_config('mod_zoom');
        //Zoom API credentials from https://developer.zoom.us/me/
        $key = '<zoom_api_key>';
        $secret = '<zoom_api_secret>';
        $token = array(
            "iss" => $key,
            // The benefit of JWT is expiry tokens, we'll set this one to expire in 1 minute
            "exp" => time() + 60
        );
        return JWT::encode($token, $secret);
    }

}


