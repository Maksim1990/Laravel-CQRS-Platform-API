@update-tag
Feature: Update tag
    In order to use application
    As an application user
    I need to be able to update tag

    Background:
        Given the "Content-Type" request header is "application/json"
        Then I get behat token
        And I save it into "behatToken"
        Given the "Authorization" request header is "Bearer <<behatToken>>"

        # Create first tag
        Given the request body is:
        """
        {
           "name":"Test tag FIRST",
           "description": "Test description tag FIRST"
        }
        """
        When I send request to "/tags" using HTTP POST
        And the response code is 201

        # Create second video
        Then the request body is:
        """
        {
           "name":"Test tag SECOND",
           "description": "Test description tag SECOND"
        }
        """
        When I send request to "/tags" using HTTP POST
        And the response code is 201

    Scenario: Update tag with valid data
        When I send request to "/tags" using HTTP GET
        And the response code is 200
        And the response body contains JSON:
        """
        {
          "data": [
                 {
                    "type": "tags",
                    "id": 1,
                    "attributes": {
                       "name":"Test tag FIRST",
                        "description": "Test description tag FIRST",
                        "user_id": 1,
                        "created_at": "@variableType(string)",
                        "updated_at": "@variableType(string)"
                    }
                },
                {
                    "type": "tags",
                    "id": 2,
                    "attributes": {
                        "name":"Test tag SECOND",
                        "description": "Test description tag SECOND",
                        "user_id": 1,
                        "created_at": "@variableType(string)",
                        "updated_at": "@variableType(string)"
                    }
                }
           ]
        }
        """

        Then the request body is:
        """
        {
           "name":"Test tag SECOND UPDATED",
           "description": "Test description tag SECOND UPDATED"
        }
        """
        When I send request to "/tags/2" using HTTP PATCH
        And the response code is 200
        And the response body contains JSON:
        """
        {
            "data": {
                "name":"Test tag SECOND UPDATED",
                "description": "Test description tag SECOND UPDATED"
            }
        }
        """

        When I send request to "/tags/2" using HTTP GET
        And the response code is 200
        And the response body contains JSON:
        """
        {
            "data": {
                "id": 2,
                "name":"Test tag SECOND UPDATED",
                "description": "Test description tag SECOND UPDATED",
                "user_id": "@variableType(integer)",
                "created_at": "@variableType(string)",
                "updated_at": "@variableType(string)"
            }
        }
        """

        When I send request to "/tags/1" using HTTP GET
        And the response code is 200
        And the response body contains JSON:
        """
        {
            "data": {
                "id": 1,
                "name":"Test tag FIRST",
                "description": "Test description tag FIRST",
                "user_id": "@variableType(integer)",
                "created_at": "@variableType(string)",
                "updated_at": "@variableType(string)"
            }
        }
        """

        Then the request body is:
        """
        {
           "name": "Test tag FIRST",
           "description":"aapkwekferf';elg[r]3lg3[lg3mf34nfi3n4nnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnn23423423423p[kp23k[23k4p[23k4p[234p[23k"
        }
        """
        When I send request to "/tags/2" using HTTP PATCH
        And the response code is 400
        And the response body contains JSON:
        """
        {
            "code": "400",
                "messages": {
                    "name": [
                        "The name has already been taken."
                    ],
                    "description": [
                        "The description may not be greater than 50 characters."
                    ]
             }
        }
        """

    Scenario: Update tag with invalid ID
        Then the request body is:
        """
        {
           "name": "Test tag name"
        }
        """
        When I send request to "/tags/INVALID-ID" using HTTP PATCH
        And the response code is 404
        And the response body contains JSON:
        """
        {
           "code": 404,
           "message": "Tag with ID INVALID-ID is not found"
        }
        """
