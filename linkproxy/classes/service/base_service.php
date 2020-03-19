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
 * Base service.
 *
 * @package     local_linkproxy
 * @copyright   2019 Titus Learning <guy.thomas@tituslearning.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_linkproxy\service;

defined('MOODLE_INTERNAL') || die;

/**
 * Mainly for adding new functionality to
 * children at a later date.
 */
abstract class base_service {

    public static function instance() : base_service {
        static $instances = [];
        $class = get_called_class();
        if (!empty($instances[$class])) {
            return $instances[$class];
        }
        $instance = new $class();
        $instances[$class] = $instance;
        return $instance;
    }
}