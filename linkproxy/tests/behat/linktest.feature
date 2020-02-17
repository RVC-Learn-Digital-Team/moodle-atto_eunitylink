@local @linkproxy @editor_atto @atto @atto_customlink
Feature: Add a link to a course using the

 @javascript

  Scenario: Teacher inserts a link
      Given the following "users" exist:
    | username | firstname | lastname | email                |
    | teacher1 | Terry1    | Teacher1 | teacher1@example.com |
    | student1 | Sam1      | Student1 | student1@example.com |

    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    # First add the Custom link button to the Atto editor
    Given I log in as "admin"
    And I navigate to "Plugins > Text editors > Atto HTML editor > Atto toolbar settings" in site administration
    And I set the field "Toolbar config" to multiline:
    """
      style1 = title, bold, italic
      list = unorderedlist, orderedlist
      links = link
      files = image, media, managefiles
      style2 = underline, strike, subscript, superscript
      align = align
      indent = indent
      insert = equation, charmap, table, clear
      undo = undo
      accessibility = accessibilitychecker, accessibilityhelper
      other = html, customlink
    """
   And I click on "Save changes" "button"
   And I log out

   # Check that teachers can use the Customlink button
   And I log in as "teacher1"
       And I am on "Course 1" course homepage with editing mode on
    And I add a "Forum" to section "1" and I fill the form with:
      | Forum name | Test forum name |
      | Forum type | Standard forum for general use |
      | Description | Test forum description |
    And I add a new discussion to "Test forum name" forum with:
      | Subject | Forum post1 |
      | Message | Test link |
    And I follow "Forum post1"
    And I click on "Edit" "link" in the "//div[contains(concat(' ', normalize-space(@class), ' '), ' forumpost ')][contains(., 'Forum post1')]" "xpath_element"
    And I select the text in the "Message" Atto editor
    Then "Customlink" "button" should exist
    And I click on "Customlink" "button"
    And I set the field "Accession number" to "469793H052909"
    And I set the field "Medical Records id/Patient id" to "469793H"
    And I click on "Create link" "button"
    And I click on "Save changes" "button"
    Then I should see "Test link"
    And I log out

    # Check that students don't see the Customlink button
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test forum name"
    And I click on "Add a new discussion topic" "button"
    Then "Customlink" "button" should not exist