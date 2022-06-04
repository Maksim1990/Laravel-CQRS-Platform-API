@auth-token
Feature: Check application auth token
    In order to use application
    As an application user
    I need to make sure auth routes are not accessible without token

    Background:
        Given the "Content-Type" request header is "application/json"

    Scenario: Try to create lesson without auth token
        Then the request body is:
        """
        {
           "name" : "Lesson name"
        }
        """
        When I send request to "/lessons" using HTTP POST
        And the response code is 401
        And the response body contains JSON:
        """
        {
           "code": 401,
           "message": "Authorization token must be provided"
        }
        """
