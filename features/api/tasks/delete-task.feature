@delete-task
Feature: Delete task
    In order to use application
    As an application user
    I need to be able to delete task

    Background:
        Given the "Content-Type" request header is "application/json"
        Then I get behat token
        And I save it into "behatToken"
        Given the "Authorization" request header is "Bearer <<behatToken>>"


    Scenario: Delete lesson by specific ID
        When I send request to "/tasks/INVALID-ID" using HTTP DELETE
        And the response code is 404
        And the response body contains JSON:
        """
        {
           "code": 404,
           "message": "Task with ID INVALID-ID is not found"
        }
        """

    Scenario: Delete lesson by specific ID
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
           "title":"Test task",
           "description":"This is test task",
           "lesson_id": 1
        }
        """
        When I send request to "/tasks" using HTTP POST
        And the response code is 201

        When I send request to "/tasks/1" using HTTP DELETE
        And the response code is 200
        And the response body contains JSON:
        """
        {
          "status":"Task deleted"
        }
        """
