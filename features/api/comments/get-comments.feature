@get-comments
Feature: Get comments
    In order to use application
    As an application user
    I need to be able to get comments

    Background:
        Given the "Content-Type" request header is "application/json"
        Then I get behat token
        And I save it into "behatToken"
        Given the "Authorization" request header is "Bearer <<behatToken>>"

    Scenario: Get all comments and by specific ID when no comments exist
        When I send request to "/comments" using HTTP GET
        And the response code is 200
        And the response body contains JSON:
        """
        {
         "data": []
        }
        """

        When I send request to "/comments/INVALID-ID" using HTTP GET
        And the response code is 404
        And the response body contains JSON:
        """
        {
           "code": 404,
           "message": "Comment with ID INVALID-ID is not found"
        }
        """

    Scenario: Get all comments and by specific ID
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
           "message":"My first comment",
           "type":"COURSE",
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
                        "message": "My first comment",
                        "model_id": 1,
                        "type": "COURSE",
                        "user_id": "@variableType(integer)",
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

        When I send request to "/comments/1" using HTTP GET
        And the response code is 200
        And the response body contains JSON:
        """
        {
            "data": {
                "id": 1,
                "message": "My first comment",
                "type": "COURSE",
                "model_id": 1,
                "user_id": "@variableType(integer)",
                "created_at": "@variableType(string)",
                "updated_at": "@variableType(string)"
            }
        }
        """
