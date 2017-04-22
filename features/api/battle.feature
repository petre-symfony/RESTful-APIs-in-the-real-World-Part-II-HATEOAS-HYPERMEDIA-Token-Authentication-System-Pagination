Feature:
  In order to prove my programmers' worth against projects
  As an API client
  I need to be able to create and view battles

  Background:
    Given the user "weaverryan" exists
    And "weaverryan" has an authentication token "ABCD123"
    And I set the "Authorization" header to be "token ABCD123"

  Scenario: Create a battle
    Given there is a programmer called "Fred"
    And there is a project called "my_project"
    Given I have the payload:
      """
      {
        "programmerId": "%programmers.Fred.id%",
        "projectId" : "%projects.my_project.id%"
      }
      """
    When I request "POST /api/battles"
    Then the response status code should be 201
    And the "Location" header should exist
    And the "didProgrammerWin" property should exist