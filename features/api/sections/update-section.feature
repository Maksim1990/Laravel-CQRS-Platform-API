@update-section
Feature: Update section
    In order to use application
    As an application user
    I need to be able to update section

    Background:
        Given the "Content-Type" request header is "application/json"
        Then I get behat token
        And I save it into "behatToken"
        Given the "Authorization" request header is "Bearer <<behatToken>>"

        Given the request body is:
        """
        {
           "name":"Test course name",
           "slug":"test-course",
           "description":"Test course description"
        }
        """
        When I send request to "/courses" using HTTP POST
        And the response code is 201

        # Create first section
        Then the request body is:
        """
        {
           "name":"Test first section name",
           "course_id": 1,
           "description":"Test first section description"
        }
        """
        When I send request to "/sections" using HTTP POST
        And the response code is 201
        And the response body contains JSON:
        """
        {
            "data": {
                "name":"Test first section name"
            }
        }
        """

        # Create second section
        Then the request body is:
        """
        {
           "name":"Test second section name",
           "course_id": 1,
           "description":"Test second section description"
        }
        """
        When I send request to "/sections" using HTTP POST
        And the response code is 201
        And the response body contains JSON:
        """
        {
            "data": {
                "name":"Test second section name"
            }
        }
        """

    Scenario: Update section with valid data
        When I send request to "/sections" using HTTP GET
        And the response code is 200
        And the response body contains JSON:
        """
        {
          "data": [
                 {
                    "type": "sections",
                    "id": 1,
                    "attributes": {
                        "name": "Test first section name",
                        "description": "Test first section description",
                        "course_id": "@variableType(integer)",
                        "user_id": "@variableType(integer)",
                        "created_at": "@variableType(string)",
                        "updated_at": "@variableType(string)"
                    }
                },
                {
                    "type": "sections",
                    "id": 2,
                    "attributes": {
                        "name": "Test second section name",
                        "description": "Test second section description",
                        "course_id": "@variableType(integer)",
                        "user_id": "@variableType(integer)",
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
           "name":"Test section name UPDATED",
           "description":"Test section description UPDATED"
        }
        """
        When I send request to "/sections/1" using HTTP PATCH
        And the response code is 200
        And the response body contains JSON:
        """
        {
            "data": {
                "name":"Test section name UPDATED",
                "description":"Test section description UPDATED"
            }
        }
        """

        When I send request to "/sections/1" using HTTP GET
        And the response code is 200
        And the response body contains JSON:
        """
        {
            "data": {
                "id": 1,
                "name":"Test section name UPDATED",
                "description":"Test section description UPDATED",
                "course_id": "@variableType(integer)",
                "user_id": "@variableType(integer)",
                "created_at": "@variableType(string)",
                "updated_at": "@variableType(string)"
            }
        }
        """

    Scenario: Update section with already taken name
        Then the request body is:
        """
        {
           "name":"Test second section name",
           "description":"aapkwekferf';elg[r]3lg3[lg3mf34nfi3n4nnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnn23423423423p[kp23k[23k4p[23k4p[234p[23k"
        }
        """
        When I send request to "/sections/1" using HTTP PATCH
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

    Scenario: Update section with invalid ID
        Then the request body is:
        """
        {
           "name":"Test section name"
        }
        """
        When I send request to "/sections/INVALID-ID" using HTTP PATCH
        And the response code is 404
        And the response body contains JSON:
        """
        {
           "code": 404,
           "message": "Section with ID INVALID-ID is not found"
        }
        """
