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
require_once($CFG->dirroot . '/local/searchbytags/yaml_parser/spyc.php');
require_once($CFG->dirroot . '/local/searchbytags/classes/ExistsFilter.php');

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


//        $this->nottags = optional_param_array('nottags', array(), PARAM_TEXT);
//        if ( (!empty($this->nottags)) && $this->nottags[0] == null) {
//            array_shift($this->nottags);
//        }
//        $this->locfilter = optional_param('locfilter', '', PARAM_TEXT);
//        $this->locvalue = optional_param('locsearchval', '', PARAM_TEXT);
//        if ( (!empty($this->tags)) || (!empty($this->nottags)) || ((!empty($this->locfilter)) && (!empty($this->locvalue))) ) {
//            $this->init();
//        }
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

//        $tags = $this->get_tags_used();
//        $attr = array (
//                          'multiple' => 'true',
//                          'class' => 'searchoptions large'
//                      );
//        if (count($tags) > 10) {
//            $attr['size'] = 10;
//        }
//        echo html_writer::label('Show questions with tags:', 'tags[]');
//        echo "<br />\n";
//        echo html_writer::select($tags, 'tags[]', $this->tags, array('' => '--show all--'), $attr);
//        echo "<br />\n";
//        echo html_writer::label('Show questions WITHOUT tags:', 'tags[]');
//        echo "<br />\n";
//        echo html_writer::select($tags, 'nottags[]', $this->nottags, array('' => '--show all--'), $attr);
//        echo "<br />\n";

        $this->display_add_filter_controls();
    }


    private function display_add_filter_controls() {
        global $PAGE;

        $meta_tags = $this->get_meta_tags();

        echo "<br />\n";
        echo html_writer::label('Current Filters:', 'filters');
        echo "<textarea name='filters' rows='4' cols='30' id='current_filters'>$this->filters</textarea>";

        echo "<br>";
        echo html_writer::label('Add Filter:', 'filter_name');
        echo html_writer::empty_tag('input', array('id' => 'filter_name'));
        echo "<div id='filter_controls'><select id='filter_combobox' style='width: 210px' size='4'>";
        foreach ($meta_tags as $meta_tag) {
            echo "<option value='$meta_tag'>$meta_tag</option>";
        }
        echo "</select>";

        echo html_writer::select(array("Exists"), 'filter','', array('' => 'choosedots'),array('id' => 'filter_type'));

        echo"<div id='filter_type_controls'></div></div>";

        $PAGE->requires->yui_module('moodle-local_searchbytags-filter', 'M.local_searchbytags.filter.init');
    }

    private function display_loc_filter() {
        echo html_writer::label('Filter LOC:', 'locsearchval');
        echo html_writer::select(array(1=>'>', 2=>'=', 3=>'<'), 'locfilter', $this->locfilter);
        echo html_writer::empty_tag('input', array('name' => 'locsearchval', 'id' => 'locsearchval',
                                                   'class' => 'searchoptions', 'value' => $this->locvalue));
    }

    private function init() {
        global $DB;

//        $this->params = array();
//        if (!empty($this->tags)) {
//            if (! is_numeric($this->tags[0]) ) {
//                list($tagswhere, $tagsparams) = $DB->get_in_or_equal($this->tags, SQL_PARAMS_NAMED, 'tag');
//                $tagids = $DB->get_fieldset_select('tag', 'id', 'name ' . $tagswhere, $tagsparams);
//            } else {
//                $tagids = $this->tags;
//            }
//            list($where, $this->params) = $DB->get_in_or_equal($tagids, SQL_PARAMS_NAMED, 'tag');
//            $this->where = "(SELECT COUNT(*) as tagcount FROM {tag_instance} ti WHERE itemid=q.id AND tagid $where)=".
//                       count($this->tags);
//        }
//
//        if (!empty($this->nottags)) {
//            if (!is_numeric($this->nottags[0])) {
//                list($tagswhere, $tagsparams) = $DB->get_in_or_equal($this->nottags, SQL_PARAMS_NAMED, 'tag');
//                $tagids = $DB->get_fieldset_select('tag', 'id', 'name ' . $tagswhere, $tagsparams);
//            } else {
//                $tagids = $this->nottags;
//            }
//            list($where, $params) = $DB->get_in_or_equal($tagids, SQL_PARAMS_NAMED, 'tag');
//            if (!empty($this->where)) {
//                $this->where .= " AND ";
//            }
//            $this->where .= "(SELECT COUNT(*) as tagcount FROM {tag_instance} ti WHERE itemid=q.id AND tagid $where)=0";
//            $this->params = array_merge($this->params, $params);
//        }

        if(!empty($this->filters)) {
            $filters = explode("\n", $this->filters);

            //primitive example can be seen below
            //for each filter
                //identify what type of filter it is
                //create filter with nifty "new $filter_name"
                //apply filter

            $tag = explode(" ", $filters[0])[0];

            $exist_filter = new ExistsFilter($tag);
            $where = $exist_filter->apply_filter();

            if (!empty($this->where)) {
                $this->where .= " AND ";
            }

            $this->where .= $where;

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

    private function filter_loc() {
        global $DB;

        $catId = explode(",", optional_param('category', '', PARAM_TEXT))[0];
        $result = $DB->get_records_select("question","category = $catId");

        $matching_questions = array();
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
            $metatag = spyc_load($yaml_metatag);

            if (isset($metatag['LOC'])) {
                if ($this->locfilter == 1) {
                    if ($metatag['LOC'] > $this->locvalue) {
                        $matching_questions[] = $qid;
                    }
                }

                if ($this->locfilter == 2) {
                    if ($metatag['LOC'] == $this->locvalue) {
                        $matching_questions[] = $qid;
                    }
                }

                if ($this->locfilter == 3) {
                    if ($metatag['LOC'] < $this->locvalue) {
                        $matching_questions[] = $qid;
                    }
                }
            }
        }


        if (!empty($this->where)) {
            $this->where .= " AND ";
        }

        if (!empty($matching_questions)) {
            $where = "q.id ";
            if (count($matching_questions) == 1){
                $where .= "= ".$matching_questions[0];
            }
            else {
                $where .= "IN (" . implode(',', $matching_questions) . ")";
            }

            $this->where .= $where;
        }
        else {
            $this->where .= "q.id IN (-1)";
        }
    }

    private function get_tags_used() {
        global $DB;
        $categories = $this->get_categories();
        list($catidtest, $params) = $DB->get_in_or_equal($categories, SQL_PARAMS_NAMED, 'cat');
        $sql = "SELECT name as value, name as display FROM {tag} WHERE id IN
                (
                 SELECT DISTINCT tagi.tagid FROM {tag_instance} tagi, {question}
                         WHERE itemtype='question' AND {question}.id=tagi.itemid AND category $catidtest
                )
                AND name NOT LIKE 'META%'
                ORDER BY name";
        return $DB->get_records_sql_menu($sql, $params);
    }

    protected function get_current_category($categoryandcontext) {
        global $DB;
        list($categoryid, $contextid) = explode(',', $categoryandcontext);
        if (!$categoryid) {
            return false;
        }

        if (!$category = $DB->get_record('question_categories',
                array('id' => $categoryid, 'contextid' => $contextid))) {
            return false;
        }
        return $category;
    }

    private function get_categories() {
        $cmid = optional_param('cmid', 0, PARAM_INT);
        $categoryparam = optional_param('category', '', PARAM_TEXT);
        $courseid = optional_param('courseid', 0, PARAM_INT);

        if ($cmid) {
            list($thispageurl, $contexts, $cmid, $cm, $quiz, $pagevars) = question_edit_setup('editq', '/mod/quiz/edit.php', true);
            if ($pagevars['cat']) {
                $categoryparam = $pagevars['cat'];
            }
        }

        if ($categoryparam) {
            $catandcontext = explode(',', $categoryparam);
            $cats = question_categorylist($catandcontext[0]);
            return $cats;
        } else if ($cmid) {
            list($module, $cm) = get_module_from_cmid($cmid);
            $courseid = $cm->course;
            require_login($courseid, false, $cm);
            $thiscontext = context_module::instance($cmid);
        } else {
            $module = null;
            $cm = null;
            if ($courseid) {
                $thiscontext = context_course::instance($courseid);
            } else {
                $thiscontext = null;
            }
        }

        $cats = get_categories_for_contexts($thiscontext->id);
        return array_keys($cats);
    }
}
