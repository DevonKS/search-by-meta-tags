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

defined('MOODLE_INTERNAL') || die;

abstract class QuestionFilter {
    protected $filter_type;

    public function apply_filter() {
        $catId = explode(",", optional_param('category', '', PARAM_TEXT))[0];
        $questions = $this->get_questions($catId);
        $questions = $this->format_questions($questions);

        $matching_questions = $this->filter_type->filter($questions);

        return $this->get_where_statement($matching_questions);
    }

    private function get_questions($catId)
    {
        global $DB;

        $questions = $DB->get_records_select("question", "category = $catId");

        $children_categories = $DB->get_records_select('question_categories', "parent = $catId", array(), '', 'id');
        foreach ($children_categories as $child) {
            $questions = array_merge($questions, $this->get_questions($child->id));
        }

        return $questions;
    }

    abstract protected function format_questions($questions);

    protected function get_where_statement($matching_questions) {
        $where = '';

        if (!empty($matching_questions)) {
            $where .= "q.id ";
            if (count($matching_questions) == 1){
                $where .= "= ".$matching_questions[0];
            }
            else {
                $where .= "IN (" . implode(',', $matching_questions) . ")";
            }
        }
        else {
            $where .= "q.id IN (-1)";
        }

        return $where;
    }

}