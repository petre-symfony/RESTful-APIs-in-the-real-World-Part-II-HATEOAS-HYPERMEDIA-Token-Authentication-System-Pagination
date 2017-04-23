Feature: Programmer
  In order to battle projects
  As an API client
  I need to be able to create programmers and power them up

  Background:
    Given the user "weaverryan" exists
    And "weaverryan" has an authentication token "ABCD123"
    And I set the "Authorization" header to be "token ABCD123"

  Scenario: Create a programmer
    Given I have the payload:
      """
      {
        "nickname": "ObjectOrienter",
        "avatarNumber" : "2",
        "tagLine": "I'm from a test!"
      }
      """
    When I request "POST /api/programmers"
    Then the response status code should be 201
    And the "Location" header should be "/api/programmers/ObjectOrienter"
    And the "nickname" property should equal "ObjectOrienter"

  Scenario: Validation errors
    Given I have the payload:
      """
      {
        "avatarNumber" : "2",
        "tagLine": "I'm from a test!"
      }
      """
    When I request "POST /api/programmers"
    Then the response status code should be 400
    And the following properties should exist:
      """
      type
      title
      errors
      """
    And the "errors.nickname" property should exist
    But the "errors.avatarNumber" property should not exist
    And the "Content-Type" header should be "application/problem+json"

  Scenario: Error response on invalid JSON
    Given I have the payload:
      """
      {
        "avatarNumber" : "2
        "tagLine": "I'm from a test!"
      }
      """
    When I request "POST /api/programmers"
    Then the response status code should be 400
    And the "Content-Type" header should be "application/problem+json"
    And the "type" property should contain "invalid_body_format"

  Scenario: Proper 404 exception on no programmer
    When I request "GET /api/programmers/fake"
    Then the response status code should be 404
    And the "Content-Type" header should be "application/problem+json"
    And the "type" property should equal "about:blank"
    And the "title" property should equal "Not Found"
    And the "detail" property should contain "programmer has deserted"

  Scenario: GET one programmer
    Given the following programmers exist:
      | nickname   | avatarNumber |
      | UnitTester | 3            |
    When I request "GET /api/programmers/UnitTester"
    Then the response status code should be 200
    And the following properties should exist:
      """
      nickname
      avatarNumber
      powerLevel
      tagLine
      """
    And the "nickname" property should equal "UnitTester"
    And the "userId" property should not exist
    #And the "_links.self.href" property should equal "/api/programmers/UnitTester"
    And the link "self" should exist and its value should be "/api/programmers/UnitTester"
    

  Scenario: GET a collection of programmers
    Given the following programmers exist:
      | nickname    | avatarNumber |
      | UnitTester  | 3            |
      | CowboyCoder | 5            |
    When I request "GET /api/programmers"
    Then the response status code should be 200
    And the "_embedded.programmers" property should be an array
    And the "_embedded.programmers" property should contain 2 items
    And the "_embedded.programmers.0.nickname" property should equal "UnitTester"

  # 5 per page  
  Scenario: I am able to paginate through the programmer resources
    Given the following programmers exist:
      | nickname     |
      | Programmer 1 |
      | Programmer 2 |
      | Programmer 3 |
      | Programmer 4 |
      | Programmer 5 |
      | Programmer 6 |
      | Programmer 7 |
      | Programmer 8 |
      | Programmer 9 |
      | Programmer 10|
      | Programmer 11|
      | Programmer 12|
    When I request "GET /api/programmers"
    And I follow the "next" link
    And the "_embedded.programmers" property should contain "Programmer7"
    And the "_embedded.programmers" property should not contain "Programmer2"
    And the "_embedded.programmers" property should not contain "Programmer11"

  Scenario: Get a collection of battles for a programmer
    Given there is a project called "ProjectA"
    And there is a project called "ProjectB"
    And there is a programmer called "Fred"
    And there has been a battle between "Fred" and "ProjectA"
    And there has been a battle between "Fred" and "ProjectB"
    When I request "GET /api/programmers/Fred/battles"
    Then the response status code should be 200
    And the "_embedded.battles" property should be an array
    And the "_embedded.battles" property should contain 2 items
    And the "_embedded.battles.0.didProgrammerWin" property should exist
    

  Scenario: PUT to update a programmer
    Given the following programmers exist:
      | nickname    | avatarNumber | tagLine |
      | CowboyCoder | 5            | foo     |
    And I have the payload:
      """
      {
        "nickname": "CowgirlCoder",
        "avatarNumber" : 2,
        "tagLine": "foo"
      }
      """
    When I request "PUT /api/programmers/CowboyCoder"
    Then the response status code should be 200
    And the "avatarNumber" property should equal "2"
    But the "nickname" property should equal "CowboyCoder"

  Scenario: PATCH to update a programmer
    Given the following programmers exist:
      | nickname    | avatarNumber | tagLine | powerLevel |
      | CowboyCoder | 5            | foo     | 4          |
    And I have the payload:
      """
      {
        "tagLine": "bar"
      }
      """
    When I request "PATCH /api/programmers/CowboyCoder"
    Then the response status code should be 200
    And the "avatarNumber" property should equal "5"
    And the "tagLine" property should equal "bar"

  Scenario: DELETE a programmer
    Given the following programmers exist:
      | nickname   | avatarNumber |
      | UnitTester | 3            |
    When I request "DELETE /api/programmers/UnitTester"
    Then the response status code should be 204
