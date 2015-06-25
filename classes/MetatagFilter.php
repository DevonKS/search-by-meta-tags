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

    private function get_question_metatags($catId) {
        global $DB;

        $result = $DB->get_records_select("question","category = $catId");

        $metatags = array();
        foreach ($result as $question) {
            $qid = $question->id;
            $sql = "SELECT t.rawname
                    FROM {question} q, {tag} t, {tag_instance} ti
                    WHERE q.id = ti.itemid AND t.id = ti.tagid AND q.id = ?";

            $tags = $DB->get_records_sql($sql, array($qid));
            $base64_metatag = "";
            foreach ($tags as $id => $tag_part) {
                if (substr($tag_part->rawname, 0, 4) == "META") {
                    $base64_metatag .= substr($tag_part->rawname, 4);
                }
            }

            $yaml_metatag = base64_decode($base64_metatag);
            $metatag = spyc_load($yaml_metatag);

            if (is_a($this->filter_type, 'ExistsFilter')) {
                $metatags[$qid] = $metatag;
            }
            else {
                $value = '';
                if (isset($metatag[$this->filter_tag])) $value = $metatag[$this->filter_tag];
                $metatags[$qid] = $value;
            }
        }

        return $metatags;
    }

    protected function get_question_data()
    {
        $catId = explode(",", optional_param('category', '', PARAM_TEXT))[0];
        return $this->get_question_metatags($catId);
    }
}