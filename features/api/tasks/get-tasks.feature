@get-tasks
Feature: Get tasks
    In order to use application
    As an application user
    I need to be able to get tasks

    Background:
        Given the "Content-Type" request header is "application/json"
        Then I get behat token
        And I save it into "behatToken"
        Given the "Authorization" request header is "Bearer <<behatToken>>"

    Scenario: Get all tasks and by specific ID when no tasks exist
        When I send request to "/tasks" using HTTP GET
        And the response code is 200
        And the response body contains JSON:
        """
        {
         "data": []
        }
        """

        When I send request to "/tasks/INVALID-ID" using HTTP GET
        And the response code is 404
        And the response body contains JSON:
        """
        {
           "code": 404,
           "message": "Task with ID INVALID-ID is not found"
        }
        """

    Scenario: Get all tasks and by specific ID
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

        Then the request body is:
        """
        {
           "title":"Test task",
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
                "title":"Test task"
            }
        }
        """

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
                        "title": "Test task",
                        "user_id": "@variableType(integer)",
                        "lesson_id": "@variableType(integer)",
                        "description": "This is test task",
                        "created_at": "@variableType(string)",
                        "updated_at": "@variableType(string)"
                    }
                }
           ]
        }
        """

        # Add comment and tag relations
        Given the request body is:
        """
        {
           "message":"My first comment",
           "type":"TASK",
           "model_id": 1
        }
        """
        When I send request to "/comments" using HTTP POST
        And the response code is 201
        And the response body contains JSON:
        """
        {
            "data": {
                "message":"My first comment"
            }
        }
        """

        Given the request body is:
        """
        {
           "name" :"Test task tag",
           "description": "Test task description tag",
           "type": "TASK",
           "model_id": 1
        }
        """
        When I send request to "/tags" using HTTP POST
        And the response code is 201
        And the response body contains JSON:
        """
        {
            "data": {
                "name":"Test task tag"
            }
        }
        """

        When I send request to "/tasks/1?relationships=lesson;comments;tags;user" using HTTP GET
        And the response code is 200
        And the response body contains JSON:
        """
        {
            "data": {
                "id": 1,
                "title": "Test task",
                "description": "This is test task",
                "user_id": "@variableType(integer)",
                "lesson_id": "@variableType(integer)",
                "created_at": "@variableType(string)",
                "updated_at": "@variableType(string)",
                "relationships": {
                    "lesson": {
                        "data": [
                            {
                                "id": 1,
                                "name": "Test lesson name",
                                "course_id": 1,
                                "user_id": 1,
                                "description": "Test lesson description",
                                "created_at": "@variableType(string)",
                                "updated_at": "@variableType(string)"
                            }
                        ]
                   },
                   "comments": {
                        "data": [
                            {
                                "id": 1,
                                "message": "My first comment",
                                "commentable_id": 1,
                                "commentable_type": "TASK",
                                "created_at": "@variableType(string)",
                                "updated_at": "@variableType(string)"
                            }
                        ]
                   },
                   "tags": {
                        "data": [
                            {
                                "id": 1,
                                "name": "Test task tag",
                                "description": "Test task description tag",
                                "created_at": "@variableType(string)",
                                "updated_at": "@variableType(string)"
                            }
                        ]
                   },
                   "user": {
                        "data": [
                            {
                                "id": 1,
                                "name": "@variableType(string)",
                                "created_at": "@variableType(string)",
                                "updated_at": "@variableType(string)"
                            }
                        ]
                   }
               }
            }
        }
        """
