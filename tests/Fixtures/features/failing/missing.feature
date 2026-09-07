Feature: Failing
  Scenario: Missing text on page
    Given I am on "/index.html"
    Then I should see "Not there"
