@update-comment
Feature: Update comment
    In order to use application
    As an application user
    I need to be able to update comment

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

        Given the request body is:
        """
        {
           "name":"Test lesson name",
           "description":"Test lesson description",
           "course_id": 1
        }
        """
        When I send request to "/lessons" using HTTP POST
        And the response code is 201

        # Create first comment
        Given the request body is:
        """
        {
           "message":"My first comment",
           "type":"LESSON",
           "model_id": 1
        }
        """
        When I send request to "/comments" using HTTP POST
        And the response code is 201
        And the response body contains JSON:
        """
        {
            "data": {
                "message":"My first comment"
            }
        }
        """

        # Create second comment
        Then the request body is:
        """
        {
           "message":"My second comment",
           "type":"LESSON",
           "model_id": 1
        }
        """
        When I send request to "/comments" using HTTP POST
        And the response code is 201
        And the response body contains JSON:
        """
        {
            "data": {
                "message":"My second comment"
            }
        }
        """

    Scenario: Update comment with valid data
        When I send request to "/comments" using HTTP GET
        And the response code is 200
        And the response body contains JSON:
        """
        {
          "data": [
                 {
                    "type": "comments",
                    "id": 1,
                    "attributes": {
                        "message":"My first comment",
                        "user_id": "@variableType(integer)",
                        "model_id": 1,
                        "type": "LESSON",
                        "created_at": "@variableType(string)",
                        "updated_at": "@variableType(string)",
                        "user": {
                            "id": 1,
                            "name": "@variableType(string)",
                            "created_at": "@variableType(string)",
                            "updated_at": "@variableType(string)"
                        }
                    }
                },
                {
                    "type": "comments",
                    "id": 2,
                    "attributes": {
                        "message":"My second comment",
                        "user_id": "@variableType(integer)",
                        "model_id": 1,
                        "type": "LESSON",
                        "created_at": "@variableType(string)",
                        "updated_at": "@variableType(string)",
                        "user": {
                            "id": 1,
                            "name": "@variableType(string)",
                            "created_at": "@variableType(string)",
                            "updated_at": "@variableType(string)"
                        }
                    }
                }
           ]
        }
        """

        Then the request body is:
        """
        {
           "message":"My first comment UPDATED",
           "type":"COURSE"
        }
        """
        When I send request to "/comments/2" using HTTP PATCH
        And the response code is 200
        And the response body contains JSON:
        """
        {
            "data": {
                "message":"My first comment UPDATED"
            }
        }
        """

        When I send request to "/comments/2" using HTTP GET
        And the response code is 200
        And the response body contains JSON:
        """
        {
            "data": {
                "id": 2,
                "message": "My first comment UPDATED",
                "type": "COURSE",
                "model_id": 1,
                "user_id": "@variableType(integer)",
                "created_at": "@variableType(string)",
                "updated_at": "@variableType(string)"
            }
        }
        """

        Then the request body is:
        """
        {
           "type":"INVALID"
        }
        """
        When I send request to "/comments/1" using HTTP PATCH
        And the response code is 400
        And the response body contains JSON:
        """
        {
            "code": "400",
            "messages": {
                 "type": [
                    "The selected type is invalid."
                ]
            }
        }
        """

    Scenario: Update comment with invalid ID
        Then the request body is:
        """
        {
           "message": "Test message"
        }
        """
        When I send request to "/comments/INVALID-ID" using HTTP PATCH
        And the response code is 404
        And the response body contains JSON:
        """
        {
           "code": 404,
           "message": "Comment with ID INVALID-ID is not found"
        }
        """
