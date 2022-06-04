@create-lesson
Feature: Create lesson
    In order to use application
    As an application user
    I need to be able to create lesson

    Background:
        Given the "Content-Type" request header is "application/json"
        Then I get behat token
        And I save it into "behatToken"
        Given the "Authorization" request header is "Bearer <<behatToken>>"

    Scenario: Create a lesson with missing description
        Then the request body is:
        """
        {
           "name" : "Lesson name test"
        }
        """
        When I send request to "/lessons" using HTTP POST
        And the response code is 400
        And the response body contains JSON:
        """
        {
            "code": "400",
            "messages": {
                "course_id": [
                    "The course id field is required."
                ]
            }
        }
        """

    Scenario: Create a lesson with missing name
        Then the request body is:
        """
        {
           "description":"Test description"
        }
        """
        When I send request to "/lessons" using HTTP POST
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

    Scenario: Create a lesson with missing data
        Then the request body is:
        """
        {}
        """
        When I send request to "/lessons" using HTTP POST
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

    Scenario: Create a lesson with valid data
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
        And the response body contains JSON:
        """
        {
            "data": {
                "name":"Test lesson name"
            }
        }
        """
