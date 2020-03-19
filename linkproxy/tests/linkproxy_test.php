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
 * File containing tests for linkproxy_test.
 *
 * @package     local_linkproxy
 * @category    test
 * @copyright   2019 Marcus Green <marcusgreen@tituslearning.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();


/**
 * The linkproxy_test test class.
 *
 * @package    local_linkproxy
 * @copyright  Titus Learning 2020 by Marcus Green
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class local_linkproxy_linkproxy_test_ extends advanced_testcase {

    // Populated in setup.
    private $linkrecord = null;
    public function test_upsert_link() {
        $this->resetAfterTest();
        $this->setAdminUser();
        global $DB;
        $proxy = new local_linkproxy\service\proxy();
        $accessionnumber = '2';

        // Check link creation.
        $linkhash = $proxy->upsert_link($accessionnumber);
        $this->assertInternalType('string', $linkhash, 'hashedlink is not a string');
        $linkrecord = $DB->get_record('local_linkproxy', ['linkhash' => $linkhash]);
        $this->assertEquals($linkhash, $linkrecord->linkhash);
        $this->assertEquals($accessionnumber, $linkrecord->accessionnumber);

        // Check link update.
        $accessionnumber = '200';
        $linkhash = $proxy->upsert_link($accessionnumber, $linkhash);
        $dbvals = $proxy->get_dbvals($linkhash);
        $this->assertEquals($accessionnumber, $dbvals->accessionnumber);

    }

    public function test_get_token() {
        global $CFG;
        if (!isset($CFG->phpunit_islocal)) {
            $this->markTestSkipped();
        }
        $this->resetAfterTest();
        $proxy = new local_linkproxy\service\proxy();
        $data = $proxy->get_token();
        // Request should return array with this in one of the elements.
        $this->assertEquals($data['token_type'], 'Bearer');

    }
    public function test_get_image() {
        global $CFG;
        if (!isset($CFG->phpunit_islocal)) {
            $this->markTestSkipped();
        }
        $this->resetAfterTest();
        $this->setAdminUser();
        $proxy = new local_linkproxy\service\proxy();
        $token = $proxy->get_token();
        $imagelink = $proxy->get_imagelink($token, $this->linkrecord);
        $this->assertStringStartsWith('http', $imagelink->link);
    }

    public function test_get_dbvals() {
        $this->resetAfterTest();
        $this->setAdminUser();
        $proxy = new local_linkproxy\service\proxy();
        $dbvals = $proxy->get_dbvals($this->linkrecord->linkhash);
        $this->assertEquals($dbvals->linkhash, $this->linkrecord->linkhash);
    }

    /**
     * used by  test_get_dbvals
     *
     * @return void
     */
    public function setup() {
        set_config('clientid', 'eUnity_moodle_titus_test', 'local_linkproxy');
        global $DB;
        $this->setAdminUser();
        $proxy = new local_linkproxy\service\proxy();
        $accessionnumber = '469793H052909';
        $linkhash = $proxy->upsert_link($accessionnumber);
        $this->linkrecord = $DB->get_record('local_linkproxy', ['linkhash' => $linkhash]);
    }
}
