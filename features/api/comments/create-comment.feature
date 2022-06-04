@create-comment
Feature: Create comment
    In order to use application
    As an application user
    I need to be able to create comment

    Background:
        Given the "Content-Type" request header is "application/json"
        Then I get behat token
        And I save it into "behatToken"
        Given the "Authorization" request header is "Bearer <<behatToken>>"

    Scenario: Create a task with missing data
        Then the request body is:
        """
        {}
        """
        When I send request to "/comments" using HTTP POST
        And the response code is 400
        And the response body contains JSON:
        """
        {
            "code": "400",
                "messages": {
                    "message": [
                        "The message field is required."
                    ],
                    "type": [
                        "The type field is required."
                    ],
                    "model_id": [
                        "The model id field is required."
                    ]
            }
        }
        """

    Scenario: Create a comment with valid data
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
