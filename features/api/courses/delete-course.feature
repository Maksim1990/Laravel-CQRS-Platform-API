@delete-course
Feature: Delete course
    In order to use application
    As an application user
    I need to be able to delete course

    Background:
        Given the "Content-Type" request header is "application/json"
        Then I get behat token
        And I save it into "behatToken"
        Given the "Authorization" request header is "Bearer <<behatToken>>"


    Scenario: Delete course by specific ID
        When I send request to "/courses/INVALID-ID" using HTTP DELETE
        And the response code is 404
        And the response body contains JSON:
        """
        {
           "code": 404,
           "message": "Course with ID INVALID-ID is not found"
        }
        """

    Scenario: Delete course by specific ID
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

        When I send request to "/courses/test-course" using HTTP GET
        And the response code is 200

        When I send request to "/courses/test-course" using HTTP DELETE
        And the response code is 200
        And the response body contains JSON:
        """
        {
          "status":"Course deleted"
        }
        """
