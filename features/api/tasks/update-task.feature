@update-task
Feature: Update task
    In order to use application
    As an application user
    I need to be able to update task

    Background:
        Given the "Content-Type" request header is "application/json"
        Then I get behat token
        And I save it into "behatToken"
        Given the "Authorization" request header is "Bearer <<behatToken>>"

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

        # Create first task
        Then the request body is:
        """
        {
           "title":"Test task 1",
           "description":"This is test task",
           "lesson_id": 1
        }
        """
        When I send request to "/tasks" using HTTP POST
        And the response code is 201
        And the response body contains JSON:
        """
        {
            "data": {
                "title":"Test task 1"
            }
        }
        """

        # Create second task
        Then the request body is:
        """
        {
           "title":"Test task 2",
           "description":"This is second test task",
           "lesson_id": 1
        }
        """
        When I send request to "/tasks" using HTTP POST
        And the response code is 201
        And the response body contains JSON:
        """
        {
            "data": {
                "title":"Test task 2"
            }
        }
        """

    Scenario: Update task with valid data
        When I send request to "/tasks" using HTTP GET
        And the response code is 200
        And the response body contains JSON:
        """
        {
          "data": [
                 {
                    "type": "tasks",
                    "id": 1,
                    "attributes": {
                        "title": "Test task 1",
                        "user_id": "@variableType(integer)",
                        "lesson_id": "@variableType(integer)",
                        "description": "This is test task",
                        "created_at": "@variableType(string)",
                        "updated_at": "@variableType(string)"
                    }
                },
                {
                    "type": "tasks",
                    "id": 2,
                    "attributes": {
                        "title": "Test task 2",
                        "user_id": "@variableType(integer)",
                        "lesson_id": "@variableType(integer)",
                        "description": "This is second test task",
                        "created_at": "@variableType(string)",
                        "updated_at": "@variableType(string)"
                    }
                }
           ]
        }
        """

        Then the request body is:
        """
        {
           "title":"Test task name updated",
           "description":"Test task description updated"
        }
        """
        When I send request to "/tasks/1" using HTTP PATCH
        And the response code is 200
        And the response body contains JSON:
        """
        {
            "data": {
                "title":"Test task name updated",
                "description":"Test task description updated"
            }
        }
        """

        When I send request to "/tasks/1" using HTTP GET
        And the response code is 200
        And the response body contains JSON:
        """
        {
            "data": {
                "id": 1,
                "title": "Test task name updated",
                "description": "Test task description updated",
                "user_id": "@variableType(integer)",
                "lesson_id": "@variableType(integer)",
                "created_at": "@variableType(string)",
                "updated_at": "@variableType(string)"
            }
        }
        """

    Scenario: Update task with already taken name
        Then the request body is:
        """
        {
           "title":"Test task 2",
           "description":"aapkwekferf';elg[r]3lg3[lg3mf34nfi3n4nnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnn23423423423p[kp23k[23k4p[23k4p[234p[23k"
        }
        """
        When I send request to "/tasks/1" using HTTP PATCH
        And the response code is 400
        And the response body contains JSON:
        """
        {
            "code": "400",
            "messages": {
                "title": [
                    "The title has already been taken."
                ],
                "description": [
                    "The description may not be greater than 50 characters."
                ]
            }
        }
        """

    Scenario: Update task with invalid ID
        Then the request body is:
        """
        {
           "title":"Test task name 2"
        }
        """
        When I send request to "/tasks/INVALID-ID" using HTTP PATCH
        And the response code is 404
        And the response body contains JSON:
        """
        {
           "code": 404,
           "message": "Task with ID INVALID-ID is not found"
        }
        """
