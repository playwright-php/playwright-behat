Feature: Demo Playwright Extension
  As a developer
  I want to test web applications
  So that I can ensure they work correctly

  Background:
    Given I am on "https://example.com"

  Scenario: Basic navigation and content verification
    Then I should see "Example Domain"
    And the page title should be "Example Domain"
    And the current URL should be "https://example.com/"
    When I take a screenshot named "example-homepage"

  Scenario: Element visibility checks
    Then "h1" should be visible
    And "body" should be visible
    And "non-existent-element" should not be visible
