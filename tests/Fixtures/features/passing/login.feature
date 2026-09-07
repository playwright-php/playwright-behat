Feature: Login
  Scenario: Fill and submit the form
    Given I am on "/index.html"
    When I fill "#email" with "user@test.com"
    And I fill "#password" with "secret"
    And I click on "#login-button"
    Then I should see "Dashboard ready"
    And I take a screenshot named "after login"

  Scenario: Storage does not leak from the previous scenario
    Given I am on "/index.html"
    Then I should see "Welcome"

  Scenario Outline: Pages by path
    Given I am on "<path>"
    Then I should see "<text>"

    Examples:
      | path        | text            |
      | /index.html | Welcome         |
      | /login.html | Dashboard ready |
