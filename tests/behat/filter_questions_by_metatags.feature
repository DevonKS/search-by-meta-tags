@local @local_searchbymetatags
Feature: Filter questions by meta-tags
  In order to quickly find a particular question
  As a teacher
  I need to filter questions based on their meta-tags

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | teacher   | 1        | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | weeks  |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    And the following "question categories" exist:
      | contextlevel | reference | questioncategory | name                 |
      | Course       | C1        | Top              | Programming Contests |
    And the following "questions" exist:
      | questioncategory     | qtype | name                       | questiontext                  |
      | Programming Contests | essay | question with meta-tags    | Write about whatever you want |
      | Programming Contests | essay | question without meta-tags | some question text            |
    And I log in as "teacher1"
    And I follow "Course 1"
    And I navigate to "Questions" node in "Course administration > Question bank"

    And I click on "Edit" "link" in the "question with meta-tags" "table_row"
    And I follow "Tags"
    And I set the field "Other tags" to "meta;;LOC: 5, meta;;Difficulty: Hard"
    And I press "id_submitbutton"

    And I set the field "Select a category" to "Programming Contests"

  @javascript
  Scenario: Using the exists filter
    When I set the field "Filter Attribute" to "LOC"
    And I set the field "filter_combobox" to "LOC"
    And I set the field "Filter Type" to "Exists"
    And I press "Add Filter"

    Then I should see "question with meta-tags"
    And I should not see "question without meta-tags"

  @javascript
  Scenario: Using the doesn't exist filter
    When I set the field "Filter Attribute" to "LOC"
    And I set the field "filter_combobox" to "LOC"
    And I set the field "Filter Type" to "Doesn't Exist"
    And I press "Add Filter"

    Then I should see "question without meta-tags"
    And I should not see "question with meta-tags"

  @javascript
  Scenario: Using the contains filter
    When I set the field "Filter Attribute" to "Diff"
    And I set the field "filter_combobox" to "Difficulty"
    And I set the field "Filter Type" to "Contains"
    And I set the field "Contains" to "Hard"
    And I press "Add Filter"

    Then I should see "question with meta-tags"
    And I should not see "question without meta-tags"

  @javascript
  Scenario: Using the doesn't contain filter
    When I set the field "Filter Attribute" to "Diff"
    And I set the field "filter_combobox" to "Difficulty"
    And I set the field "Filter Type" to "Contains"
    And I set the field "Contains" to "Easy"
    And I press "Add Filter"

    Then I should not see "question with meta-tags"
    And I should not see "question without meta-tags"

  @javascript
  Scenario: Using the greater than filter
    When I set the field "Filter Attribute" to "LOC"
    And I set the field "filter_combobox" to "LOC"
    And I set the field "Filter Type" to "Greater Than"
    And I set the field "value" to "3"
    And I press "Add Filter"

    Then I should see "question with meta-tags"
    And I should not see "question without meta-tags"

  @javascript
  Scenario: Using the greater than filter2
    When I set the field "Filter Attribute" to "LOC"
    And I set the field "filter_combobox" to "LOC"
    And I set the field "Filter Type" to "Greater Than"
    And I set the field "value" to "5"
    And I press "Add Filter"

    Then I should not see "question with meta-tags"
    And I should not see "question without meta-tags"


  @javascript
  Scenario: Using the greater than or equal to filter
    When I set the field "Filter Attribute" to "LOC"
    And I set the field "filter_combobox" to "LOC"
    And I set the field "Filter Type" to "Greater Than or Equal To"
    And I set the field "value" to "5"
    And I press "Add Filter"

    Then I should see "question with meta-tags"
    And I should not see "question without meta-tags"

  @javascript
  Scenario: Using the greater than or equal to filter2
    When I set the field "Filter Attribute" to "LOC"
    And I set the field "filter_combobox" to "LOC"
    And I set the field "Filter Type" to "Greater Than or Equal To"
    And I set the field "value" to "6"
    And I press "Add Filter"

    Then I should not see "question with meta-tags"
    And I should not see "question without meta-tags"

  @javascript
  Scenario: Using the less than filter
    When I set the field "Filter Attribute" to "LOC"
    And I set the field "filter_combobox" to "LOC"
    And I set the field "Filter Type" to "Less Than"
    And I set the field "value" to "7"
    And I press "Add Filter"

    Then I should see "question with meta-tags"
    And I should not see "question without meta-tags"

  @javascript
  Scenario: Using the less than filter2
    When I set the field "Filter Attribute" to "LOC"
    And I set the field "filter_combobox" to "LOC"
    And I set the field "Filter Type" to "Less Than"
    And I set the field "value" to "5"
    And I press "Add Filter"

    Then I should not see "question with meta-tags"
    And I should not see "question without meta-tags"

  @javascript
  Scenario: Using the less than or equal to filter
    When I set the field "Filter Attribute" to "LOC"
    And I set the field "filter_combobox" to "LOC"
    And I set the field "Filter Type" to "Less Than or Equal To"
    And I set the field "value" to "5"
    And I press "Add Filter"

    Then I should see "question with meta-tags"
    And I should not see "question without meta-tags"

  @javascript
  Scenario: Using the less than or equal to filter2
    When I set the field "Filter Attribute" to "LOC"
    And I set the field "filter_combobox" to "LOC"
    And I set the field "Filter Type" to "Less Than or Equal To"
    And I set the field "value" to "4"
    And I press "Add Filter"

    Then I should not see "question with meta-tags"
    And I should not see "question without meta-tags"

  @javascript
  Scenario: Using the equal to filter
    When I set the field "Filter Attribute" to "LOC"
    And I set the field "filter_combobox" to "LOC"
    And I set the field "Filter Type" to "Equal To"
    And I set the field "value" to "5"
    And I press "Add Filter"

    Then I should see "question with meta-tags"
    And I should not see "question without meta-tags"

  @javascript
  Scenario: Using the equal to filter2
    When I set the field "Filter Attribute" to "LOC"
    And I set the field "filter_combobox" to "LOC"
    And I set the field "Filter Type" to "Equal To"
    And I set the field "value" to "6"
    And I press "Add Filter"

    Then I should not see "question with meta-tags"
    And I should not see "question without meta-tags"