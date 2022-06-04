@delete-section
Feature: Delete section
    In order to use application
    As an application user
    I need to be able to delete section

    Background:
        Given the "Content-Type" request header is "application/json"
        Then I get behat token
        And I save it into "behatToken"
        Given the "Authorization" request header is "Bearer <<behatToken>>"


    Scenario: Delete section by specific ID
        When I send request to "/sections/INVALID-ID" using HTTP DELETE
        And the response code is 404
        And the response body contains JSON:
        """
        {
           "code": 404,
           "message": "Section with ID INVALID-ID is not found"
        }
        """

    Scenario: Delete section by specific ID
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

        When I send request to "/sections/1" using HTTP GET
        And the response code is 200

        When I send request to "/sections/1" using HTTP DELETE
        And the response code is 200
        And the response body contains JSON:
        """
        {
          "status":"Section deleted"
        }
        """
