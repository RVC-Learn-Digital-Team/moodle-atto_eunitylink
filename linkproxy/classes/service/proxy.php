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
 * Link proxy service.
 *
 * @package     local_linkproxy
 * @copyright   2019 Titus Learning Marcus Green
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_linkproxy\service;

defined('MOODLE_INTERNAL') || die();

global $CFG;

use curl;

require_once($CFG->libdir . '/filelib.php');

class proxy extends base_service {
    const GRANT_TYPE = 'client_credentials';

    /**
     * This method is just here for IDE auto completion.
     * @return email
     */
    public static function instance() : base_service {
        return parent::instance();
    }

    /**
     * Given an accession number
     * taken from the atto_eunitylink button processing
     * return the hash that will be used in the link by
     * the get_redirect function. If it is an existing
     * link pass in the hash and update the record.
     * @param string $an //accession number
     * @param string $hash hashtring from existing record
     * @return string
     */
    public function upsert_link(string $an, string $hash = '') {
        global $DB;
        if ($hash) {
            $id = $DB->get_field('local_linkproxy', 'id', ['linkhash' => $hash]);
            $DB->update_record('local_linkproxy', ['id' => $id, 'accessionnumber' => $an]);
            return $hash;
        } else {
            $length = 8;
            $linkhash = strtoupper(substr(bin2hex(openssl_random_pseudo_bytes($length)), 0, $length));
            $record = ['linkhash' => $linkhash, 'accessionnumber' => $an];
            $DB->insert_record('local_linkproxy', $record);
            return $linkhash;
        }
    }

    /**
     * Get record from database. Used to populate
     * editing form from an existing link
     *
     * @param string $hash
     * @return object
     */
    public function get_dbvals(string $hash) : \stdClass {
        global $DB;
        $result = $DB->get_record('local_linkproxy', ['linkhash' => $hash]);
        return $result ?: (object) [];

    }


    /**
     * Given hash passed in as a parameter on a clicked link
     * look up the url for the external image link and redirect to it.
     * If there is something wrong with the record/link redirect to the
     * generic viewer page with error message on it.
     *
     * @param string $hash
     * @return void
     */
    public function get_redirect(string $hash) {
        global $DB;
        if (!$record = $DB->get_record('local_linkproxy', ['linkhash' => $hash])) {
            redirect('https://eunity.rvc.ac.uk/e/viewer');
        }

        $token = $this->get_token();
        if (array_key_exists('error', $token)) {
          echo($token['error'].PHP_EOL);
          die();
        } else {
           $image = $this->get_imagelink($token, $record);
        }
        if (isset($image->link)) {
            redirect($image->link);
        } else {
            // Shows error saying one of the past parameters is incorrect.
            redirect('https://eunity.rvc.ac.uk/e/viewer');
        }
    }

    /**
     * Pass in the token and information from db
     * perform a redirect to the eUnity image viewer.
     *
     * @param array $token
     * @param \stdClass $record
     * @return \stdClass
     */
    public function get_imagelink(array $token, \stdClass $record) {
        global $USER;
        $apiurl = get_config('local_linkproxy', 'apiurl');
        $params = [
            'accession_number' => $record->accessionnumber,
            'hide_demographics' => false,
            'user' => $USER->username
        ];
        $curl = new curl();
        $headers = [
            "Content-Type: application/json; charset=utf-8",
            "Authorization: Bearer ".$token['access_token']
        ];
        $options = [
            'CURLOPT_HTTPHEADER' => $headers,
        ];

        $response = $curl->get($apiurl, $params, $options);
        $imagelink = json_decode($response);
        return $imagelink;

    }
    /**
     * Get the jwt token
     *
     * @return string
     */
    public function get_token() : array {
        $params = [
            'client_id'     => get_config('local_linkproxy', 'clientid'),
            'client_secret' => get_config('local_linkproxy', 'sharedsecretkey'),
            'scope' => get_config('local_linkproxy', 'scope'),
            'grant_type'    => self::GRANT_TYPE
        ];
        $curl = new curl();
        $identityproviderurl = get_config('local_linkproxy', 'identityproviderurl');
        $data = $curl->post($identityproviderurl, $params);
        return json_decode($data, true);
    }
}
