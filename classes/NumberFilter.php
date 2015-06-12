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

class NumberFilter extends AbstractFilter {
    private $operator;
    private $value;

    public function __construct($tag, $args) {
        $this->filter_tag = $tag;

        $this->operator = $args[0];
        $this->value = $args[1];
    }

    protected function filter($metatag)
    {
        if (isset($metatag[$this->filter_tag])) {
            if ($this->operator == '<') {
                return $metatag[$this->filter_tag] < $this->value;
            }
            else if ($this->operator == '=') {
                return $metatag[$this->filter_tag] = $this->value;
            }
            else if ($this->operator == '>') {
                return $metatag[$this->filter_tag] > $this->value;
            }
        }
        return false;
    }
}