@create-get-tag
Feature: Create and get tag
    In order to use application
    As an application user
    I need to be able to create and get a tag

    Background:
        Given the "Content-Type" request header is "application/json"
        Then I get behat token
        And I save it into "behatToken"
        Given the "Authorization" request header is "Bearer <<behatToken>>"

    Scenario: Create a tag with valid data
        Given the request body is:
        """
        {
           "name":"Test tag",
           "description": "Test description tag"
        }
        """
        When I send request to "/tags" using HTTP POST
        And the response code is 201
        And the response body contains JSON:
        """
        {
            "data": {
                "name":"Test tag"
            }
        }
        """

        When I send request to "/tags" using HTTP GET
        And the response code is 200
        And the response body contains JSON:
        """
        {
            "data": [
                {
                    "type": "tags",
                    "id": 1,
                    "attributes": {
                        "name": "Test tag",
                        "description": "Test description tag",
                        "user_id": 1,
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
           "name":"Test tag"
        }
        """
        When I send request to "/tags" using HTTP POST
        And the response code is 400
        And the response body contains JSON:
        """
        {
            "code": "400",
            "messages": {
                "name": [
                    "The name has already been taken."
                ]
            }
        }
        """

    Scenario: Create a tag with missing data
        Then the request body is:
        """
        {}
        """
        When I send request to "/tags" using HTTP POST
        And the response code is 400
        And the response body contains JSON:
        """
        {
            "code": "400",
            "messages": {
                "name": [
                    "The name field is required."
                ]
            }
        }
        """

    Scenario: Create and get tag with relationships
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

        # Add lesson relation
        Then the request body is:
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
           "name" :"Test FIRST tag",
           "description": "Test FIRST description tag",
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
                "name":"Test FIRST tag"
            }
        }
        """

        When I send request to "/courses?relationships=tags" using HTTP GET
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
                        "user_id": 1,
                        "description": "Test course description",
                        "created_at": "@variableType(string)",
                        "updated_at": "@variableType(string)"
                    },
                    "relationships": {
                        "tags": {
                            "data": [
                                {
                                    "id": 1,
                                    "name": "Test FIRST tag",
                                    "description": "Test FIRST description tag",
                                    "user_id": 1,
                                    "created_at": "@variableType(string)",
                                    "updated_at": "@variableType(string)"
                                }
                            ]
                        }
                    }
                }
            ]
        }
        """

        # Create second tag without linked model
        Given the request body is:
        """
        {
           "name" :"Test SECOND tag",
           "description": "Test SECOND description tag"
        }
        """
        When I send request to "/tags" using HTTP POST
        And the response code is 201

        # Attach new tag to the course
        Given the request body is:
        """
        {
           "tags": [2],
           "action":"ATTACH"
        }
        """
        When I send request to "/courses/test-course/tags" using HTTP PUT
        And the response code is 200
        And the response body contains JSON:
        """
        {
          "status": "Course tags updated"
        }
        """

        # Attach new tag to the lesson
        Given the request body is:
        """
        {
           "tags": [1,2]
        }
        """
        When I send request to "/lessons/1/tags" using HTTP PUT
        And the response code is 200
        And the response body contains JSON:
        """
        {
          "status": "Lesson tags updated"
        }
        """

        When I send request to "/courses/test-course/?relationships=tags" using HTTP GET
        And the response code is 200
        And the response body contains JSON:
        """
        {
          "data": {
            "relationships": {
                "tags": {
                    "data": [
                        {
                            "id": 1,
                            "name": "Test FIRST tag",
                            "description": "Test FIRST description tag",
                            "user_id": 1,
                            "created_at": "@variableType(string)",
                            "updated_at": "@variableType(string)"
                        },
                        {
                            "id": 2,
                            "name": "Test SECOND tag",
                            "description": "Test SECOND description tag",
                            "user_id": 1,
                            "created_at": "@variableType(string)",
                            "updated_at": "@variableType(string)"
                        }
                     ]
                  }
               }
            }
        }
        """

        When I send request to "/tags?relationships=courses;lessons" using HTTP GET
        And the response code is 200
        And the response body contains JSON:
        """
        {
            "data": [
                {
                    "type": "tags",
                    "id": 1,
                    "attributes": {
                        "name": "Test FIRST tag",
                        "description": "Test FIRST description tag",
                        "created_at": "@variableType(string)",
                        "updated_at": "@variableType(string)"
                    },
                    "relationships": {
                        "courses": {
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
                        "lessons": {
                            "data": [
                                {
                                    "id": 1,
                                    "name": "Test lesson name",
                                    "description": "Test lesson description",
                                    "created_at": "@variableType(string)",
                                    "updated_at": "@variableType(string)"
                                }
                            ]
                        }
                    }
                },
                {
                    "type": "tags",
                    "id": 2,
                    "attributes": {
                        "name": "Test SECOND tag",
                        "description": "Test SECOND description tag",
                        "created_at": "@variableType(string)",
                        "updated_at": "@variableType(string)"
                    },
                    "relationships": {
                        "courses": {
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
                        "lessons": {
                            "data": [
                                {
                                    "id": 1,
                                    "name": "Test lesson name",
                                    "description": "Test lesson description",
                                    "created_at": "@variableType(string)",
                                    "updated_at": "@variableType(string)"
                                }
                            ]
                        }
                    }
                }
            ]
        }
        """

        # Detach tags from the course
        Given the request body is:
        """
        {
           "tags": [1,2],
           "action":"DETACH"
        }
        """
        When I send request to "/courses/test-course/tags" using HTTP PUT
        And the response code is 200
        And the response body contains JSON:
        """
        {
          "status": "Course tags updated"
        }
        """

        When I send request to "/courses/test-course/?relationships=tags" using HTTP GET
        And the response code is 200
        And the response body contains JSON:
        """
        {
          "data": {
            "relationships": {
                "tags": {
                    "data": []
                  }
               }
            }
        }
        """
