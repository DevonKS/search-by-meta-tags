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
include_once($CFG->dirroot . '/local/searchbymetatags/classes/ExistsFilter.php');
include_once($CFG->dirroot . '/local/searchbymetatags/classes/TextFilter.php');
include_once($CFG->dirroot . '/local/searchbymetatags/classes/NumberFilter.php');
include_once($CFG->dirroot . '/local/searchbymetatags/classes/QuestionAttributeFilter.php');
include_once($CFG->dirroot . '/local/searchbymetatags/classes/MetatagFilter.php');

function local_searchbymetatags_get_question_bank_search_conditions($caller) {
    return array( new local_searchbymetatags_question_bank_search_condition($caller));
}

class local_searchbymetatags_question_bank_search_condition extends core_question\bank\search\condition {
    protected $tags;
    protected $where;
    protected $params;
    protected $filters;

    public function __construct() {
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        $this->filters = optional_param('filters', '', PARAM_RAW);

        if (!empty($this->filters)) {
            $this->init();
        }
    }

    private function init()
    {
        if (!empty($this->filters)) {

            $filters = explode("\n", $this->filters);

            foreach ($filters as $filter_string) {
                if ($filter_string != '' && $filter_string != "\r") {
                    $filter_args = explode(" ", $filter_string);
                    $filter_type = trim(array_pop($filter_args));
                    $tag = trim($filter_args[0], '"');
                    unset($filter_args[0]);
                    $filter_type = new $filter_type($tag, array_values($filter_args));

                    if ($tag[0] == "$") {
                        $filter = new QuestionAttributeFilter(substr($tag, 1), $filter_type);
                    } else {
                        $filter = new MetatagFilter($tag, $filter_type);
                    }
                    $where = $filter->apply_filter();

                    if (!empty($this->where)) {
                        $this->where .= " AND ";
                    }

                    $this->where .= $where;
                }
            }
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
        echo '<h4>Current Filters</h4>';
        echo "<textarea name='filters' class='searchoptions' rows='4' cols='50' id='current_filters'>$this->filters</textarea>";

        echo "<br />\n";
        echo '<div id="filter_controls"><h4>Add Filter</h4><div id="filter_attribute" style="float:left;width:250px">';
        echo html_writer::label('Filter Attribute:', 'filter_name');
        echo html_writer::empty_tag('input', array('id' => 'filter_name'));
        echo "<select id='filter_combobox' style='width: 210px' size='4'>";
        foreach ($meta_tags as $meta_tag) {
            echo "<option value='$meta_tag'>$meta_tag</option>";
        }
        echo "</select></div>";

        $filters = array("exists" => "Exists",
            "not exist" => "Doesn't Exist",
            "contains" => "Contains",
            "not contain" => "Doesn't Contain",
            "greater than" => "Greater Than",
            "greater than equal" => "Greater Than or Equal To",
            "less than" => "Less Than",
            "less than equal" => "Less Than or Equal To",
            "equal" => "Equal To");

        echo "<div id='filter_controls'>";
        echo html_writer::label('Filter Type', 'filter_type');
        echo html_writer::select($filters, 'filter','', array('' => 'choosedots'),array('id' => 'filter_type'));
        echo "<div id='filter_type_controls'></div></div>";
        $PAGE->requires->yui_module('moodle-local_searchbymetatags-meta_filter', 'M.local_searchbymetatags.meta_filter.init');
    }

    private function get_meta_tags(){
        global $DB;

        $sql = "SELECT DISTINCT t.id, t.rawname
                    FROM {tag} t, {tag_instance} ti
                    WHERE t.id = ti.tagid";

        $tags = $DB->get_records_sql($sql);
        $meta_tags = array();
        foreach ($tags as $id => $tag) {
            if (substr($tag->rawname, 0, 5) == "meta;") {
                $meta_tag_data = explode(';', $tag->rawname);
                if ($meta_tag_data[1] == 'Base64') {
                    $tag = base64_decode($meta_tag_data[2]);
                } else {
                    $tag = $meta_tag_data[2];
                }

                if (strpos($tag, '[') !== false) {
                    $tag = array(explode('[', $tag)[0] => '');
                } else {
                    $tag = yaml_parse($tag);
                }

                $meta_tags = array_merge($meta_tags, array_keys($tag));
            }
        }

        $meta_tags = array_unique($meta_tags);
        $question_attributes = array('$QuestionName', '$QuestionCategory', '$QuestionText', '$QuestionAnswer');
        $meta_tags = array_merge($meta_tags, $question_attributes);
        asort($meta_tags);

        return $meta_tags;
    }
}
