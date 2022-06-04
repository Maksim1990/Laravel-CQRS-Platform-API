@update-course
Feature: Update course
    In order to use application
    As an application user
    I need to be able to update course

    Background:
        Given the "Content-Type" request header is "application/json"
        Then I get behat token
        And I save it into "behatToken"
        Given the "Authorization" request header is "Bearer <<behatToken>>"

        # Create first course
        Given the request body is:
        """
        {
           "name":"Test course name 1",
           "slug":"test-course-first",
           "description":"Test course description"
        }
        """
        When I send request to "/courses" using HTTP POST
        And the response code is 201
        And the response body contains JSON:
        """
        {
            "data": {
                "slug": "test-course-first"
            }
        }
        """

        # Create second course
        Given the request body is:
        """
        {
           "name":"Test course name 2",
           "slug":"test-course-second",
           "description":"Test course description"
        }
        """
        When I send request to "/courses" using HTTP POST
        And the response code is 201
        And the response body contains JSON:
        """
        {
            "data": {
                "slug": "test-course-second"
            }
        }
        """

    Scenario: Update course with valid data
        When I send request to "/courses" using HTTP GET
        And the response code is 200
        And the response body contains JSON:
        """
        {
          "data": [
                 {
                    "type": "courses",
                    "id": 1,
                    "slug": "test-course-first",
                    "attributes": {
                        "name": "Test course name 1",
                        "user_id": "@variableType(integer)",
                        "description": "Test course description",
                        "created_at": "@variableType(string)",
                        "updated_at": "@variableType(string)"
                    }
                },
                {
                    "type": "courses",
                    "id": 2,
                    "slug": "test-course-second",
                    "attributes": {
                        "name": "Test course name 2",
                        "user_id": "@variableType(integer)",
                        "description": "Test course description",
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
           "name":"Test course name updated",
           "description":"Test course description updated"
        }
        """

        When I send request to "/courses/test-course-first" using HTTP PATCH
        And the response code is 200
        And the response body contains JSON:
        """
        {
            "data": {
                "name":"Test course name updated",
                "description":"Test course description updated"
            }
        }
        """

    Scenario: Update course with already taken name
        Then the request body is:
        """
        {
           "name":"Test course name 2"
        }
        """
        When I send request to "/courses/test-course-first" using HTTP PATCH
        And the response code is 400
        And the response body contains JSON:
        """
        {
            "code": "400",
            "messages": {
                "name": [
                    "The name has already been taken."
                ]
            }
        }
        """

    Scenario: Update course with invalid ID
        Then the request body is:
        """
        {
           "name":"Test course name 2"
        }
        """
        When I send request to "/courses/INVALID-ID" using HTTP PATCH
        And the response code is 404
        And the response body contains JSON:
        """
        {
           "code": 404,
           "message": "Course with ID INVALID-ID is not found"
        }
        """

    Scenario: Try to update course slug
        Then the request body is:
        """
        {
           "slug":"test-course-first-UPDATED",
           "name": "Test course name UPDATED"
        }
        """
        When I send request to "/courses/test-course-first" using HTTP PATCH
        And the response code is 200
        And the response body contains JSON:
        """
        {
            "data": {
                "slug":"test-course-first",
                "name": "Test course name UPDATED"
            }
        }
        """

        Then the request body is:
        """
        {
           "name":"Test course name UPDATED",
           "description":"aapkwekferf';elg[r]3lg3[lg3mf34nfi3n4nnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnn23423423423p[kp23k[23k4p[23k4p[234p[23k"
        }
        """
        When I send request to "/courses/test-course-second" using HTTP PATCH
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

        When I send request to "/courses/test-course-first-UPDATED" using HTTP GET
        And the response code is 404
        And the response body contains JSON:
        """
        {
           "code": 404,
           "message": "Course with ID test-course-first-UPDATED is not found"
        }
        """

        When I send request to "/courses/test-course-first" using HTTP GET
        And the response code is 200
        And the response body contains JSON:
        """
        {
            "data": {
                "id": 1,
                "slug": "test-course-first",
                "name": "Test course name UPDATED",
                "user_id": "@variableType(integer)",
                "description": "Test course description",
                "created_at": "@variableType(string)",
                "updated_at": "@variableType(string)"
            }
        }
        """
