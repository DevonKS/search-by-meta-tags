<?php
/**
 * Created by PhpStorm.
 * User: devon
 * Date: 21/07/15
 * Time: 9:57 PM
 */

include($CFG->dirroot . '/local/searchbymetatags/classes/ExistsFilter.php');

/**
 * Class local_searchbymetatags_exists_filter_testcase
 * @group local_searchbymetatags
 */
class local_searchbymetatags_exists_filter_testcase extends basic_testcase
{
    protected $list;

    protected function setUp()
    {
        $this->list = array('test' => array('test_tag' => 'present'), 'test1' => array('other_tag' => 'present'));
    }

    public function test_exists()
    {
        $exists_filter = new ExistsFilter('test_tag', 'Exists');
        $expected = array('test');

        $filtered_list = $exists_filter->filter($this->list);

        $this->assertEquals($expected, $filtered_list);
    }

    public function test_doesnt_exist()
    {
        $exists_filter = new ExistsFilter('test_tag', Array('Doesn\'t', 'Exist'));
        $expected = array('test1');

        $filtered_list = $exists_filter->filter($this->list);

        $this->assertEquals($expected, $filtered_list);
    }
}