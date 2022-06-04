@create-video
Feature: Create video
    In order to use application
    As an application user
    I need to be able to create video

    Background:
        Given the "Content-Type" request header is "application/json"
        Then I get behat token
        And I save it into "behatToken"
        Given the "Authorization" request header is "Bearer <<behatToken>>"

    Scenario: Create a video with valid data
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

        Given the request body is:
        """
        {
           "title":"Lesson's first VIDEO",
           "description":"This is a first video",
           "lesson_id": 1,
           "link":"http://example.com"
        }
        """
        When I send request to "/videos" using HTTP POST
        And the response code is 201
        And the response body contains JSON:
        """
        {
            "data": {
                "title":"Lesson's first VIDEO"
            }
        }
        """

    Scenario: Create a task with missing data
        Then the request body is:
        """
        {}
        """
        When I send request to "/tasks" using HTTP POST
        And the response code is 400
        And the response body contains JSON:
        """
        {
            "code": "400",
            "messages": {
                 "title": [
                "The title field is required."
                ],
                "lesson_id": [
                    "The lesson id field is required."
                ]
            }
        }
        """
