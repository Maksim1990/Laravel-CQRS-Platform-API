@get-videos
Feature: Get videos
    In order to use application
    As an application user
    I need to be able to get videos

    Background:
        Given the "Content-Type" request header is "application/json"
        Then I get behat token
        And I save it into "behatToken"
        Given the "Authorization" request header is "Bearer <<behatToken>>"

    Scenario: Get all videos and by specific ID when no videos exist
        When I send request to "/videos" using HTTP GET
        And the response code is 200
        And the response body contains JSON:
        """
        {
         "data": []
        }
        """

        When I send request to "/videos/INVALID-ID" using HTTP GET
        And the response code is 404
        And the response body contains JSON:
        """
        {
           "code": 404,
           "message": "Video with ID INVALID-ID is not found"
        }
        """

    Scenario: Get all videos and by specific ID
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
           "title":"Lesson's first VIDEO",
           "description":"This is a first video",
           "lesson_id": 1,
           "link":"http://example.com"
        }
        """
        When I send request to "/videos" using HTTP POST
        And the response code is 201
        And the response body contains JSON:
        """
        {
            "data": {
                "title":"Lesson's first VIDEO"
            }
        }
        """

        When I send request to "/videos" using HTTP GET
        And the response code is 200
        And the response body contains JSON:
        """
        {
          "data": [
                 {
                    "type": "videos",
                    "id": 1,
                    "attributes": {
                        "title": "Lesson's first VIDEO",
                        "user_id": "@variableType(integer)",
                        "lesson_id": "@variableType(integer)",
                        "link": "http:\/\/example.com",
                        "description": "This is a first video",
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
           "type":"VIDEO",
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
           "name" :"Test video tag",
           "description": "Test video description tag",
           "type": "VIDEO",
           "model_id": 1
        }
        """
        When I send request to "/tags" using HTTP POST
        And the response code is 201
        And the response body contains JSON:
        """
        {
            "data": {
                "name":"Test video tag"
            }
        }
        """

        When I send request to "/videos/1?relationships=lesson;comments;tags;user" using HTTP GET
        And the response code is 200
        And the response body contains JSON:
        """
        {
            "data": {
                "id": 1,
                "title": "Lesson's first VIDEO",
                "description": "This is a first video",
                "link": "http:\/\/example.com",
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
                                "commentable_type": "VIDEO",
                                "created_at": "@variableType(string)",
                                "updated_at": "@variableType(string)"
                            }
                        ]
                   },
                   "tags": {
                        "data": [
                            {
                                "id": 1,
                                "name": "Test video tag",
                                "description": "Test video description tag",
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
