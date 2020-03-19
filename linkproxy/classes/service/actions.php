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
 * @copyright   2019 Titus Learning <guy.thomas@tituslearning.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_linkproxy\service;
defined('MOODLE_INTERNAL') || die();

class actions {
    /**
     * Get the required params, call create link and return the hash
     * from the create_link call.
     * @todo should there be a permissions check or similar
     *
     * @return string
     */
    public static function create_hashedlink() {
        $proxy = new proxy();
        $an = required_param('an', PARAM_TEXT);

        return  $proxy->upsert_link($an);
    }

    /**
     * Get hash data from db table
     * Called via Ajax when customlink
     * is clicked in atto text area.
     *
     * @return \stdClass
     */
    public static function get_dbvals() {
        $proxy = new proxy();
        $hash = required_param('hash', PARAM_TEXT);
        $result = $proxy->get_dbvals($hash);

        return $result;
    }

    /**
     * update or insert new link into hash
     * table.
     *  @return \stdClass
     */
    public static function upsert_link() {
        $proxy = new proxy();
        $linkhash = optional_param('hash', '', PARAM_TEXT);
        $an = required_param('an', PARAM_TEXT);
        $hash = $proxy->upsert_link($an, $linkhash);

        return $hash;
    }
  /**
   * Get existing link based on hash from
   * url then redirect to a a new tab
   * showing the eUnity viewer
   *
   * @return void
   */
    public static function get_link() {
        $proxy = new proxy();
        $hash = required_param('hash', PARAM_TEXT);

        $proxy->get_redirect($hash);
    }
}
