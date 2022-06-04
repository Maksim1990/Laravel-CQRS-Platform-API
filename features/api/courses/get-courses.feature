@get-courses
Feature: Get courses
    In order to use application
    As an application user
    I need to be able to get courses

    Background:
        Given the "Content-Type" request header is "application/json"
        Then I get behat token
        And I save it into "behatToken"
        Given the "Authorization" request header is "Bearer <<behatToken>>"

    Scenario: Get all courses and by specific ID when no courses exist
        When I send request to "/courses" using HTTP GET
        And the response code is 200
        And the response body contains JSON:
        """
        {
         "data": []
        }
        """

        When I send request to "/courses/INVALID-ID" using HTTP GET
        And the response code is 404
        And the response body contains JSON:
        """
        {
           "code": 404,
           "message": "Course with ID INVALID-ID is not found"
        }
        """

    Scenario: Get all courses and by specific ID
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

        When I send request to "/courses" using HTTP GET
        And the response code is 200
        And the response body contains JSON:
        """
        {
            "data": [
                {
                    "type": "courses",
                    "id": 1,
                    "slug": "test-course",
                    "attributes": {
                        "name": "Test course name",
                        "user_id": "@variableType(integer)",
                        "description": "Test course description",
                        "created_at": "@variableType(string)",
                        "updated_at": "@variableType(string)"
                    }
                }
            ]
        }
        """

        # Add lesson, comment, tag and section relations
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
        And the response body contains JSON:
        """
        {
            "data": {
                "name":"Test lesson name"
            }
        }
        """

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
           "name" :"Test course tag",
           "description": "Test course description tag",
           "type": "COURSE",
           "model_id": 1
        }
        """
        When I send request to "/tags" using HTTP POST
        And the response code is 201
        And the response body contains JSON:
        """
        {
            "data": {
                "name":"Test course tag"
            }
        }
        """

        When I send request to "/courses/test-course?page=1&perPage=10&relationships=lessons;tags;sections;comments;user&relationPerPage=1" using HTTP GET
        And the response code is 200
        And the response body contains JSON:
        """
        {
            "data": {
                "id": 1,
                "slug": "test-course",
                "name": "Test course name",
                "user_id": "@variableType(integer)",
                "description": "Test course description",
                "created_at": "@variableType(string)",
                "updated_at": "@variableType(string)",
                "relationships": {
                    "lessons": {
                        "data": [
                            {
                                "id": 1,
                                "name": "Test lesson name",
                                "course_id": 1,
                                "description": "Test lesson description",
                                "created_at": "@variableType(string)",
                                "updated_at": "@variableType(string)"
                            }
                        ]
                   },
                   "sections": {
                        "data": [
                            {
                                "id": 1,
                                "name": "Test section name",
                                "description": "Test section description",
                                "course_id": 1,
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
                                "commentable_type": "COURSE",
                                "created_at": "@variableType(string)",
                                "updated_at": "@variableType(string)"
                            }
                        ]
                   },
                   "tags": {
                        "data": [
                            {
                                "id": 1,
                                "name": "Test course tag",
                                "description": "Test course description tag",
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
