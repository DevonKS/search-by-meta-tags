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

    protected function format_questions($questions)
    {
        $formatted_questions = array();
        if ($this->attribute == 'QuestionCategory') {
            $category_trees = array();
            foreach ($questions as $question) {
                $category = $question->category;
                if (!isset($category_trees[$category])) {
                    $category_tree = $this->get_category_tree($category);
                    $category_trees[$category] = $category_tree;
                } else {
                    $category_tree = $category_trees[$category];
                }
                $formatted_questions[$question->id] = $category_tree;
            }
        } else {
            $this->attribute = strtolower($this->attribute);
            $attribute = $this->attribute;
            foreach ($questions as $question) {
                $formatted_questions[$question->id] = $question->$attribute;
            }
        }

        return $formatted_questions;
    }

    private function get_category_tree($category)
    {
        global $DB;

        $current_category = $category;
        $category_tree = array();
        while ($current_category != 0) {
            $category = $DB->get_record_select('question_categories', "id = $current_category", array(), $fields = 'id, name, parent');
            $category_tree[$category->id] = $category->name;
            $current_category = $category->parent;
        }

        return implode('->', $category_tree);
    }
}