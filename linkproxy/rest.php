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
 * Plugin administration pages are defined here.
 *
 * @package     local_linkproxy
 * @copyright   Titus Learning by  Marcus Green
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_tlcore\output\rest_renderer;
use local_linkproxy\service\actions;

require_once(__DIR__.'/../../config.php');
require_login();

$output = new rest_renderer();

// Call the action on the action plans actions class.
$out = $output->call_action(actions::class);

// Output the JSON and exit.
$output->json(['result' => $out]);
$output->exit();