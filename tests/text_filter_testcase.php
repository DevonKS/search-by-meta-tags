<?php

/**
 * Created by PhpStorm.
 * User: devon
 * Date: 19/08/15
 * Time: 12:04 PM
 */

include($CFG->dirroot . '/local/searchbymetatags/classes/TextFilter.php');

/**
 * Class local_searchbymetatags_text_filter_testcase
 * @group local_searchbymetatags
 */
class local_searchbymetatags_text_filter_testcase extends basic_testcase
{
    protected $list;

    protected function setUp()
    {
        $this->list = array('test' => array('test_tag' => 'hello world'),
            'test1' => array('test_tag' => 'some test text'));
    }

    public function contains_testcase()
    {
        $test_filter = new TextFilter('test_tag', 'contains "hello"');
        $expected = array('test');

        $filtered_list = $test_filter->filter($this->list);

        $this->assertEquals($expected, $filtered_list);
    }
}