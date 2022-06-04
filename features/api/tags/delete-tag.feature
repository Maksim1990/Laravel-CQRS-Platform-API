@delete-tag
Feature: Delete tag
    In order to use application
    As an application user
    I need to be able to delete tag

    Background:
        Given the "Content-Type" request header is "application/json"
        Then I get behat token
        And I save it into "behatToken"
        Given the "Authorization" request header is "Bearer <<behatToken>>"


    Scenario: Delete tag by invalid ID
        When I send request to "/tags/INVALID-ID" using HTTP DELETE
        And the response code is 404
        And the response body contains JSON:
        """
        {
           "code": 404,
           "message": "Tag with ID INVALID-ID is not found"
        }
        """

    Scenario: Delete tag by specific ID
        Given the request body is:
        """
        {
           "name":"Test tag",
           "description": "Test description tag"
        }
        """
        When I send request to "/tags" using HTTP POST
        And the response code is 201
        And the response body contains JSON:
        """
        {
            "data": {
                "name":"Test tag"
            }
        }
        """

        When I send request to "/tags/1" using HTTP DELETE
        And the response code is 200
        And the response body contains JSON:
        """
        {
          "status":"Tag deleted"
        }
        """
