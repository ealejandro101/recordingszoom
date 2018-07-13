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

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/locallib.php');

use \Firebase\JWT\JWT;



function mod_recordingszoom_get_user_cloudrecordings_list($recordingszoom, $host_id) {
    $fi = '2018-05-05';
    $ff = '2018-06-04';
    $serviceurl = 'https://api.zoom.us/v2/users/' . $host_id . '/' . 'recordings' . '?from=' . $fi . '&to=' . $ff;
    
    $ch = curl_init($serviceurl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // add token to the authorization header
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Authorization: Bearer ' . mod_recordingszoom_generateJWT()
    ));
    $response = curl_exec($ch);
    
    if( $response->next_page_token == ''){
        
        $response = json_decode($response);
        $todas_meetings = $response->meetings;
        
        foreach ($todas_meetings as $meeting) {
            if($meeting->id == $recordingszoom->zoom_meeting_id) {
                var_dump($meeting->id);
            } else {
                echo "No es la misma\n";
            }
        }
    } else {
        // Todo hay que ir por otra pagina
        echo "estaba vacio";
    }

    
    return $response;
}




function mod_recordingszoom_get_meeting_info($recordingszoom) {
    $ch = curl_init('https://api.zoom.us/v2/meetings/' . '299176292');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // add token to the authorization header
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Authorization: Bearer ' . mod_recordingszoom_generateJWT()
    ));
    $response = curl_exec($ch);
    $response = json_decode($response);
    return $response;
}

function mod_recordingszoom_getUsers () {
    //list users endpoint GET https://api.zoom.us/v2/users
    $ch = curl_init('https://api.zoom.us/v2/users');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // add token to the authorization header
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Authorization: Bearer ' . mod_recordingszoom_generateJWT()
    ));
    $response = curl_exec($ch);
    $response = json_decode($response);
    var_dump($response);
    return $response;
}


//function to generate JWT
function mod_recordingszoom_generateJWT () {

    $config = get_config('mod_zoom');
    //Zoom API credentials from https://developer.zoom.us/me/
    $key = 'aA2E7fyITcCmKTcesXADzQ';
    $secret = 'pNHSULXLDYqC2VNWrz2foxSY8g5792sw5XeJ';
    $token = array(
        "iss" => $key,
        // The benefit of JWT is expiry tokens, we'll set this one to expire in 1 minute
        "exp" => time() + 60
    );
    return JWT::encode($token, $secret);
}




