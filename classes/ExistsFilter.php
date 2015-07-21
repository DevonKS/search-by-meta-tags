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

include_once("AbstractFilter.php");

class ExistsFilter extends AbstractFilter {
    private $exists;

    public function __construct($tag, $args) {
        $this->filter_tag = $tag;

        if ($args[0] == "doesn't") {
            $this->exists = false;
        }
        else {
            $this->exists = true;
        }
    }

    public function filter($array)
    {
        $matching_questions = array();
        foreach ($array as $id => $question) {
            if (isset($question[$this->filter_tag]) == $this->exists) {
                $matching_questions[] = $id;
            }
        }

        return $matching_questions;
    }
}