@create-course
Feature: Create course
    In order to use application
    As an application user
    I need to be able to create course

    Background:
        Given the "Content-Type" request header is "application/json"
        Then I get behat token
        And I save it into "behatToken"
        Given the "Authorization" request header is "Bearer <<behatToken>>"

    Scenario: Create a course with missing description
        Then the request body is:
        """
        {}
        """
        When I send request to "/courses" using HTTP POST
        And the response code is 400
        And the response body contains JSON:
        """
        {
            "code": "400",
            "messages": {
                 "slug": [
                    "The slug field is required."
                    ],
                 "name": [
                    "The name field is required."
                    ]
            }
        }
        """

    Scenario: Create a course with valid data
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
        And the response body contains JSON:
        """
        {
            "data": {
                "slug": "test-course"
            }
        }
        """
