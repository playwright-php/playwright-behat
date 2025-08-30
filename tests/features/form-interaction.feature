Feature: Form Interaction
  As a user
  I want to interact with forms
  So that I can submit data

  Background:
    Given I am on "file://tests/fixtures/test-page.html"

  Scenario: Fill and submit form
    When I fill "#email" with "test@example.com"
    And I fill "#password" with "password123"
    And I select "us" from "#country"
    And I check "#terms"
    And I click on "#submit-btn"
    Then I should see "Form submitted successfully!"
    And "#results" should be visible
    And "#data-table" should be visible

  Scenario: Form validation
    When I click on "#submit-btn"
    Then "#email" should have CSS class "error"

  Scenario: Table data verification
    When I fill the form with:
      | #email    | user@test.com |
      | #password | secret123     |
    And I select "ca" from "#country"
    And I check "#terms"
    And I click on "#submit-btn"
    Then I should see a table with the following data:
      | name      | email         | status   |
      | John Doe  | john@test.com | Active   |
      | Jane Smith| jane@test.com | Inactive |

  Scenario: JavaScript interaction
    When I execute JavaScript: "window.testValue = 'hello world';"
    Then the JavaScript expression "window.testValue" should return "hello world"
    And the JavaScript expression "window.testFunction('input')" should return "processed: input"
