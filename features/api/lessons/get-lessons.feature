@get-lessons
Feature: Get lessons
    In order to use application
    As an application user
    I need to be able to get lessons

    Background:
        Given the "Content-Type" request header is "application/json"
        Then I get behat token
        And I save it into "behatToken"
        Given the "Authorization" request header is "Bearer <<behatToken>>"

    Scenario: Get all lessons and by specific ID when no lessons exist
        When I send request to "/lessons" using HTTP GET
        And the response code is 200
        And the response body contains JSON:
        """
        {
         "data": []
        }
        """

        When I send request to "/lessons/INVALID-ID" using HTTP GET
        And the response code is 404
        And the response body contains JSON:
        """
        {
           "code": 404,
           "message": "Lesson with ID INVALID-ID is not found"
        }
        """

    Scenario: Get all lessons and by specific ID
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
        And the response body contains JSON:
        """
        {
            "data": {
                "name":"Test section name"
            }
        }
        """

        Then the request body is:
        """
        {
           "name":"Test lesson name",
           "description":"Test lesson description",
           "course_id": 1,
           "section_id": 1
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

        When I send request to "/lessons" using HTTP GET
        And the response code is 200
        And the response body contains JSON:
        """
        {
          "data": [
                 {
                    "type": "lessons",
                    "id": 1,
                    "attributes": {
                        "name": "Test lesson name",
                        "course_id": "@variableType(integer)",
                        "user_id": "@variableType(integer)",
                        "description": "Test lesson description",
                        "created_at": "@variableType(string)",
                        "updated_at": "@variableType(string)"
                    }
                }
           ]
        }
        """

        # Add comment,tags and task relations
        Given the request body is:
        """
        {
           "message":"My first comment",
           "type":"LESSON",
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
           "name" :"Test lesson tag",
           "description": "Test lesson description tag",
           "type": "LESSON",
           "model_id": 1
        }
        """
        When I send request to "/tags" using HTTP POST
        And the response code is 201
        And the response body contains JSON:
        """
        {
            "data": {
                "name":"Test lesson tag"
            }
        }
        """

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
        And the response body contains JSON:
        """
        {
            "data": {
                "title":"Test task"
            }
        }
        """

        When I send request to "/lessons/1?relationships=course;tasks;comments;section;tags;user" using HTTP GET
        And the response code is 200
        And the response body contains JSON:
        """
        {
            "data": {
                "id": 1,
                "name": "Test lesson name",
                "description": "Test lesson description",
                "course_id": "@variableType(integer)",
                "user_id": "@variableType(integer)",
                "created_at": "@variableType(string)",
                "updated_at": "@variableType(string)",
                "relationships": {
                    "course": {
                        "data": [
                            {
                                "id": 1,
                                "slug": "test-course",
                                "name": "Test course name",
                                "description": "Test course description",
                                "created_at": "@variableType(string)",
                                "updated_at": "@variableType(string)"
                            }
                        ]
                   },
                   "tasks": {
                        "data": [
                            {
                                "id": 1,
                                "title": "Test task",
                                "description": "This is test task",
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
                                "commentable_type": "LESSON",
                                "created_at": "@variableType(string)",
                                "updated_at": "@variableType(string)"
                            }
                        ]
                   },
                   "tags": {
                        "data": [
                            {
                                "id": 1,
                                "name": "Test lesson tag",
                                "description": "Test lesson description tag",
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
                   },
                   "section": {
                        "data": [
                            {
                                "id": 1,
                                "name": "Test section name",
                                "description": "Test section description",
                                "created_at": "@variableType(string)",
                                "updated_at": "@variableType(string)"
                            }
                        ]
                   }
               }
            }
        }
        """
