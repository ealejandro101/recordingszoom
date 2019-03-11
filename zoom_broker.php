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

use \MyFirebase\JWT\JWT;


/**
 * Función para consultar todas las grabaciones de un usuario en particular
 * y luego filtrar las grabaciones que corresponden con el id de reunion original
 * Pueden ser muchas grabaciones y solo algunas de la reunión que estamos buscando
 */
function mod_recordingszoom_get_user_cloudrecordings_list($all_zoom_meeting_ids, $host_id, $ffrom, $fto ) {

    $serviceurl = 'https://api.zoom.us/v2/users/' . $host_id . '/' . 'recordings' . '?from=' . $ffrom . '&to=' . $fto;
    
    $ch = curl_init($serviceurl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // add token to the authorization header
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Authorization: Bearer ' . mod_recordingszoom_generateJWT()
    ));
    $response = curl_exec($ch);
    $response = json_decode($response);
    $meetings_recordings = array();

    if($response->total_records > 0){
        // ToDo recorrido para la primera pagina entregada, en caso de tener más páginas se debe consultar nuevamente
            $todas_meetings = $response->meetings;
            
            // Recorrido de las meetings en la respuesta
            foreach ($todas_meetings as $meeting) { 
                // Solo se tiene en cuenta las grabaciones que son de la reunion inicial 
                if( in_array($meeting->id, $all_zoom_meeting_ids) ) { 
                    $meetings_recordings[] = $meeting;
                } 
            }
    }
    return $meetings_recordings;
}


/**
 * Función para consultar todas las grabaciones de un usuario en particular
 * y luego filtrar las grabaciones que corresponden con el id de reunion original
 * Pueden ser muchas grabaciones y solo algunas de la reunión que estamos buscando
 */
function mod_recordingszoom_get_cloudrecordings_list($zoom_meetings_id_array, $ffrom, $fto ) {

    // Todos los id configurados en el modulo
    $all_zoom_meeting_ids = array_unique($zoom_meetings_id_array);

    //Información de todas los host_id
    $all_host_id = array();

    foreach ($all_zoom_meeting_ids as $zoom_meeting_id) { 
        $meeting_info =  mod_recordingszoom_get_past_meetings_info($zoom_meeting_id);
        $all_host_id[] = $meeting_info->host_id;
    } 
    $all_host_id = array_unique($all_host_id);

    // Información de todas las grabaciones que cumplen con el criterio
    $all_meetings_recordings = array();
    foreach ($all_host_id as $host_id) {
        $new_meetings_recordings = mod_recordingszoom_get_user_cloudrecordings_list($all_zoom_meeting_ids, $host_id, $ffrom, $fto );
        $all_meetings_recordings = array_merge($all_meetings_recordings, $new_meetings_recordings);
    }

    return $all_meetings_recordings;
}




function mod_recordingszoom_get_meeting_info($zoom_meeting_id) {
    $ch = curl_init('https://api.zoom.us/v2/meetings/' . $zoom_meeting_id);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // add token to the authorization header
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Authorization: Bearer ' . mod_recordingszoom_generateJWT()
    ));
    $response = curl_exec($ch);

    if(curl_errno($ch)){
        throw new moodle_exception('errorwebservice', 'mod_recordingszoom', '', $ch->error);
    }

    $response = json_decode($response);

    $httpstatus = curl_getinfo($ch)['http_code'];
    if ($httpstatus >= 400) {
        if ($response) {
            throw new moodle_exception('errorwebservice', 'mod_recordingszoom', '', $response->message);
        } else {
            throw new moodle_exception('errorwebservice', 'mod_recordingszoom', '', "HTTP Status $httpstatus");
        }
    }


    return $response;
}



function mod_recordingszoom_get_past_meetings_info($zoom_meeting_id) {
    $ch = curl_init('https://api.zoom.us/v2/past_meetings/' . $zoom_meeting_id);
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
    return $response;
}


//function to generate JWT
function mod_recordingszoom_generateJWT () {

    $config = get_config('mod_recordingszoom');

    if (!isset($config->apiurl) || !isset($config->apikey) || !isset($config->apisecret)) {
        // Give error.
        throw new moodle_exception('errorapikeynotfound', 'mod_recordingszoom');
    }
 
    $key = $config->apikey;
    $secret = $config->apisecret;
    $token = array(
        "iss" => $key,
        // The benefit of JWT is expiry tokens, we'll set this one to expire in 1 minute
        "exp" => time() + 60
    );
    return JWT::encode($token, $secret);
}




