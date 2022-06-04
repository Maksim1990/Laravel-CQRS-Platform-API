@delete-video
Feature: Delete video
    In order to use application
    As an application user
    I need to be able to delete video

    Background:
        Given the "Content-Type" request header is "application/json"
        Then I get behat token
        And I save it into "behatToken"
        Given the "Authorization" request header is "Bearer <<behatToken>>"


    Scenario: Delete video by specific ID
        When I send request to "/videos/INVALID-ID" using HTTP DELETE
        And the response code is 404
        And the response body contains JSON:
        """
        {
           "code": 404,
           "message": "Video with ID INVALID-ID is not found"
        }
        """

    Scenario: Delete video by specific ID
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
           "title":"Lesson's first VIDEO ",
           "description":"This is a first video",
           "lesson_id": 1,
           "link":"http://example.com"
        }
        """
        When I send request to "/videos" using HTTP POST
        And the response code is 201

        When I send request to "/videos/1" using HTTP DELETE
        And the response code is 200
        And the response body contains JSON:
        """
        {
          "status":"Video deleted"
        }
        """
