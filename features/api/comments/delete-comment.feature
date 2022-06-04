@delete-comment
Feature: Delete comment
    In order to use application
    As an application user
    I need to be able to delete comment

    Background:
        Given the "Content-Type" request header is "application/json"
        Then I get behat token
        And I save it into "behatToken"
        Given the "Authorization" request header is "Bearer <<behatToken>>"


    Scenario: Delete comment by specific ID
        When I send request to "/comments/INVALID-ID" using HTTP DELETE
        And the response code is 404
        And the response body contains JSON:
        """
        {
           "code": 404,
           "message": "Comment with ID INVALID-ID is not found"
        }
        """

    Scenario: Delete comment by specific ID
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

        When I send request to "/comments/1" using HTTP DELETE
        And the response code is 200
        And the response body contains JSON:
        """
        {
          "status":"Comment deleted"
        }
        """
