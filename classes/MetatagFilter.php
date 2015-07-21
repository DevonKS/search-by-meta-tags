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

class MetatagFilter extends QuestionFilter{
    private $filter_tag;

    function __construct($filter_tag, $filter_type)
    {
        $this->filter_tag = $filter_tag;
        $this->filter_type = $filter_type;
    }

    protected function format_questions($questions)
    {
        $formatted_questions = array();
        foreach ($questions as $question) {
            $metatags = $this->get_question_metatags($question->id);
            $formatted_questions[$question->id] = $metatags;
        }

        return $formatted_questions;
    }

    private function get_question_metatags($qid)
    {
        global $DB;

        $sql = "SELECT t.rawname
                FROM {question} q, {tag} t, {tag_instance} ti
                WHERE q.id = ti.itemid AND t.id = ti.tagid AND q.id = ?";

        $tags = $DB->get_records_sql($sql, array($qid));
        $meta_tag = "";
        foreach ($tags as $id => $tag_part) {
            if (substr($tag_part->rawname, 0, 5) == "meta;") {
                $meta_tag_data = explode(';', $tag_part->rawname);
                if ($meta_tag_data[1] == 'Base64') {
                    $meta_tag .= "\n" . base64_decode($meta_tag_data[2]);
                } else if ($meta_tag_data[1] == '') {
                    $meta_tag .= "\n" . $meta_tag_data[2];
                }
            }
        }

        $meta_tag = spyc_load($meta_tag);

        if (is_a($this->filter_type, 'ExistsFilter')) {
            $metatags = $meta_tag;
        } else {
            $value = '';
            if (isset($meta_tag[$this->filter_tag])) $value = $meta_tag[$this->filter_tag];
            $metatags = $value;
        }

        return $metatags;
    }
}