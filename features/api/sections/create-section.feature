@create-section
Feature: Create section
    In order to use application
    As an application user
    I need to be able to section section

    Background:
        Given the "Content-Type" request header is "application/json"
        Then I get behat token
        And I save it into "behatToken"
        Given the "Authorization" request header is "Bearer <<behatToken>>"

    Scenario: Create a section with missing description
        Then the request body is:
        """
        {}
        """
        When I send request to "/sections" using HTTP POST
        And the response code is 400
        And the response body contains JSON:
        """
        {
            "code": "400",
            "messages": {
                 "name": [
                    "The name field is required."
                 ],
                 "course_id": [
                    "The course id field is required."
                 ]
            }
        }
        """

    Scenario: Create a section with valid data
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
           "name":"Test section name",
           "course_id": 1,
           "description":"Test section description"
        }
        """
        When I send request to "/sections" using HTTP POST
        And the response code is 201
        And the response body contains JSON:
        """
        {
            "data": {
                "name":"Test section name"
            }
        }
        """
