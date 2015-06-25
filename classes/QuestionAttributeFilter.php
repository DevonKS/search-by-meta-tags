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

include_once("QuestionFilter.php");

class QuestionAttributeFilter extends QuestionFilter{
    private $attribute;

    public function __construct($attribute, $filter_type)
    {
        $this->attribute = $attribute;
        $this->filter_type = $filter_type;
    }

    protected function get_question_data()
    {
        global $DB;

        $catId = explode(",", optional_param('category', '', PARAM_TEXT))[0];
        $questions = array();
        if ($this->attribute == 'category') {
            $current_questions = $DB->get_records_select('context', array('category' => $catId), $fields = 'category,'.$this->attribute);
            foreach ($current_questions as $id => $current_question) {
                $category_path = $DB->get_record_select('context', array('id' => $current_question->category),$fields='path');
                $categories = array_slice (explode('/', $category_path), 1);
                $category_string = '';
                foreach ($categories as $category) {
                    $category_string .= $DB->get_record_select('categories', array('id' => $category), $field='name');
                }
                $questions[$id] = $category_string;
            }
        }
        else {
            $questions = $DB->get_records_select('context', array('category' => $catId), $fields = $this->attribute);
        }

        return $questions;
    }
}