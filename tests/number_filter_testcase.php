<?php
/**
 * Created by PhpStorm.
 * User: devon
 * Date: 21/07/15
 * Time: 9:57 PM
 */

include($CFG->dirroot . '/local/searchbymetatags/classes/NumberFilter.php');

/**
 * Class local_searchbymetatags_number_filter_testcase
 * @group local_searchbymetatags
 */
class local_searchbymetatags_number_filter_testcase extends basic_testcase
{
    protected $list;

    protected function setUp()
    {
        $this->list = array('test' => array('test_tag' => 6),
            'test1' => array('test_tag' => 4),
            'test2' => array('test_tag' => 5));
    }

    public function test_lessthan()
    {
        $number_filter = new NumberFilter('test_tag', '< 5');
        $expected = array('test1');

        $filtered_list = $number_filter->filter($this->list);

        $this->assertEquals($expected, $filtered_list);
    }

    public function test_lessthanorequal()
    {
        $number_filter = new NumberFilter('test_tag', '<= 5');
        $expected = array('test1', 'test2');

        $filtered_list = $number_filter->filter($this->list);

        $this->assertEquals($expected, $filtered_list);
    }
}