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

abstract class AbstractFilter {
    protected $filter_tag;

    abstract protected function filter($metatag); //Returns the where statement?

    public function apply_filter() {
        $catId = explode(",", optional_param('category', '', PARAM_TEXT))[0];
        $metatags = $this->get_question_metatags($catId);

        $matching_questions = array();
        foreach($metatags as $qid => $metatag) {
            if ($this->filter($metatag)) {
                $matching_questions[] = $qid;
            }
        }

        return $this->get_where_statement($matching_questions);
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
            $metatags[$qid] = $metatag;
        }

        return $metatags;
    }

    private function get_where_statement($matching_questions) {
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