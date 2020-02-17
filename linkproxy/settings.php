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
 * Placeholder, no settings yet
 *
 * @package     local_linkproxy
 * @category    admin
 * @copyright   Titus Learning 2019 by  Marcus Green
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
if ($hassiteconfig) {
    if ($ADMIN->fulltree) {
        $settings = new admin_settingpage('local_linkproxy', new lang_string('settings', 'local_linkproxy'));
        $settings->add(new admin_setting_configtext(
            'local_linkproxy/apiurl',
            get_string('apiurl', 'local_linkproxy'),
            get_string('apiurl_text', 'local_linkproxy'),
            'https://eunity.api.rvc.ac.uk/viewer'
        ));
        $settings->add(new admin_setting_configtext(
            'local_linkproxy/identityproviderurl',
            get_string('identityproviderurl', 'local_linkproxy'),
            get_string('identityproviderurl_text', 'local_linkproxy'),
            'https://genids.rvc.ac.uk/connect/token'
        ));
        $settings->add(new admin_setting_configtext(
            'local_linkproxy/sharedsecretkey',
            get_string('sharedsecretkey', 'local_linkproxy'),
            get_string('sharedsecretkey_text', 'local_linkproxy'),
            'copulate-domain-shred'
        ));
        $settings->add(new admin_setting_configtext(
            'local_linkproxy/clientid',
            get_string('clientid', 'local_linkproxy'),
            get_string('clientid_text', 'local_linkproxy'),
            'eUnity_moodle_client_test'
        ));

        $settings->add(new admin_setting_configtext(
            'local_linkproxy/scope',
            get_string('scope', 'local_linkproxy'),
            get_string('scope_text', 'local_linkproxy'),
            'eUnity_api'
        ));
        $ADMIN->add('localplugins', $settings);
    }
}