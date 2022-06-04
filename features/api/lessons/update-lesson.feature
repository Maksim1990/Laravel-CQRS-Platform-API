@update-lesson
Feature: Update lesson
    In order to use application
    As an application user
    I need to be able to update lesson

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

        # Create first lesson
        Then the request body is:
        """
        {
           "name":"Test lesson name 1",
           "description":"Test lesson description",
           "course_id": 1
        }
        """
        When I send request to "/lessons" using HTTP POST
        And the response code is 201
        And the response body contains JSON:
        """
        {
            "data": {
                "name":"Test lesson name 1"
            }
        }
        """

        # Create second lesson
        Then the request body is:
        """
        {
           "name":"Test lesson name 2",
           "description":"Test lesson description 2",
           "course_id": 1
        }
        """
        When I send request to "/lessons" using HTTP POST
        And the response code is 201
        And the response body contains JSON:
        """
        {
            "data": {
                "name":"Test lesson name 2"
            }
        }
        """

    Scenario: Update lesson with valid data
        When I send request to "/lessons" using HTTP GET
        And the response code is 200
        And the response body contains JSON:
        """
        {
          "data": [
                 {
                    "type": "lessons",
                    "id": 1,
                    "attributes": {
                        "name": "Test lesson name 1",
                        "course_id": "@variableType(integer)",
                        "user_id": "@variableType(integer)",
                        "description": "Test lesson description",
                        "created_at": "@variableType(string)",
                        "updated_at": "@variableType(string)"
                    }
                },
                {
                    "type": "lessons",
                    "id": 2,
                    "attributes": {
                        "name": "Test lesson name 2",
                        "course_id": "@variableType(integer)",
                        "user_id": "@variableType(integer)",
                        "description": "Test lesson description 2",
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
           "name":"Test lesson name updated",
           "description":"Test lesson description updated"
        }
        """
        When I send request to "/lessons/1" using HTTP PATCH
        And the response code is 200
        And the response body contains JSON:
        """
        {
            "data": {
                "name":"Test lesson name updated",
                "description":"Test lesson description updated"
            }
        }
        """

        When I send request to "/lessons/1" using HTTP GET
        And the response code is 200
        And the response body contains JSON:
        """
        {
            "data": {
                "id": 1,
                "name": "Test lesson name updated",
                "description": "Test lesson description updated",
                "course_id": "@variableType(integer)",
                "user_id": "@variableType(integer)",
                "created_at": "@variableType(string)",
                "updated_at": "@variableType(string)"
            }
        }
        """

    Scenario: Update lesson with already taken name
        Then the request body is:
        """
        {
           "name":"Test lesson name 2",
           "description":"aapkwekferf';elg[r]3lg3[lg3mf34nfi3n4nnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnn23423423423p[kp23k[23k4p[23k4p[234p[23k"
        }
        """
        When I send request to "/lessons/1" using HTTP PATCH
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

    Scenario: Update lesson with invalid ID
        Then the request body is:
        """
        {
           "name":"Test lesson name 2"
        }
        """
        When I send request to "/lessons/INVALID-ID" using HTTP PATCH
        And the response code is 404
        And the response body contains JSON:
        """
        {
           "code": 404,
           "message": "Lesson with ID INVALID-ID is not found"
        }
        """
