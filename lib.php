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

global $CFG;
require_once($CFG->dirroot . '/question/editlib.php');
include_once($CFG->dirroot . '/local/searchbytags/yaml_parser/spyc.php');
include_once($CFG->dirroot . '/local/searchbytags/classes/ExistsFilter.php');
include_once($CFG->dirroot . '/local/searchbytags/classes/TextFilter.php');
include_once($CFG->dirroot . '/local/searchbytags/classes/NumberFilter.php');

function local_searchbytags_get_question_bank_search_conditions($caller) {
    return array( new local_searchbytags_question_bank_search_condition($caller));
}

function local_searchbytags_question_bank_column_types($questionbankview) {
    if ($questionbankview == 'quiz_question_bank_view') {
        return array();
    }
    return array('tags' => new local_searchbytags_question_bank_column($questionbankview));
}

class local_searchbytags_question_bank_search_condition extends core_question\bank\search\condition {
    protected $tags;
    protected $where;
    protected $params;
    protected $filters;

    public function __construct() {
        $this->filters = optional_param('filters', '', PARAM_TEXT);

        if (!empty($this->filters)) {
            $this->init();
        }
    }

    public function where() {
        return $this->where;
    }

    public function params() {
        return $this->params;
    }

    public function display_options_adv() {
        global $DB;
        global $output;
        require_login();

        $this->display_add_filter_controls();
    }


    private function display_add_filter_controls() {
        global $PAGE;

        $meta_tags = $this->get_meta_tags();

        echo "<br />\n";
        echo html_writer::label('Current Filters:', 'filters');
        echo "<textarea name='filters' rows='4' cols='50' id='current_filters'>$this->filters</textarea>";

        echo "<br>";
        echo html_writer::label('Add Filter:', 'filter_name');
        echo html_writer::empty_tag('input', array('id' => 'filter_name'));
        echo "<div id='filter_controls'><select id='filter_combobox' style='width: 210px' size='4'>";
        foreach ($meta_tags as $meta_tag) {
            echo "<option value='$meta_tag'>$meta_tag</option>";
        }
        echo "</select>";

        $filters = array("exists" => "Exists",
            "not exist" => "Doesn't Exist",
            "contains" => "Contains",
            "not contain" => "Doesn't Contain",
            "greater than" => "Greater Than",
            "greater than equal" => "Greater Than or Equal To",
            "less than" => "Less Than",
            "less than equal" => "Less Than or Equal To",
            "equal" => "Equal To");

        echo html_writer::select($filters, 'filter','', array('' => 'choosedots'),array('id' => 'filter_type'));

        echo"<div id='filter_type_controls'></div></div>";

        $PAGE->requires->yui_module('moodle-local_searchbytags-filter', 'M.local_searchbytags.filter.init');
    }

    private function init() {
        global $DB;

        if(!empty($this->filters)) {
            $filters = explode("\n", $this->filters);
            array_pop($filters); //The last element of the filters array is always and empty string

            foreach ($filters as $filter_string) {
                $filter_args = explode(" ", $filter_string);
                $filter_type = trim(array_pop($filter_args));
                $tag = trim($filter_args[0], '"');
                $args = array_slice($filter_args, 1);
                var_dump($tag);
                var_dump($filter_args);
                echo "----------------------------";

                $filter = new $filter_type($tag, $args);
                $where = $filter->apply_filter();

                if (!empty($this->where)) {
                    $this->where .= " AND ";
                }

                $this->where .= $where;
            }
        }
    }

    private function get_meta_tags(){
        global $DB;

        $result = $DB->get_records("question");

        $meta_tags = array();
        foreach ($result as $question) {
            $qid = $question->id;
            $sql = "SELECT t.rawname
                        FROM {question} q, {tag} t, {tag_instance} ti
                        WHERE q.id = ti.itemid AND t.id = ti.tagid AND q.id = ?";

            $tags = $DB->get_records_sql($sql, array($qid));

            $base64_metatag = "";
            foreach ($tags as $id => $tag) {
                if (substr($tag->rawname, 0, 4) == "META") {
                    $base64_metatag .= substr($tag->rawname, 4);
                }
            }

            $yaml_metatag = base64_decode($base64_metatag);
            if ($yaml_metatag != '') {
                $meta_tag = spyc_load($yaml_metatag);
                $meta_tags = array_merge($meta_tags, array_keys($meta_tag));
            }
        }

        $meta_tags = array_unique($meta_tags);
        asort($meta_tags);

        return $meta_tags;
    }
}
