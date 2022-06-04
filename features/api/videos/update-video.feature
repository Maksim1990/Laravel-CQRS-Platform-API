@update-video
Feature: Update video
    In order to use application
    As an application user
    I need to be able to update video

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

        # Create first video
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

        # Create second video
        Then the request body is:
        """
        {
           "title":"Lesson's second VIDEO",
           "description":"This is a second video",
           "lesson_id": 1,
           "link":"http://example2.com"
        }
        """
        When I send request to "/videos" using HTTP POST
        And the response code is 201
        And the response body contains JSON:
        """
        {
            "data": {
                "title":"Lesson's second VIDEO"
            }
        }
        """

    Scenario: Update video with valid data
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
                },
                {
                    "type": "videos",
                    "id": 2,
                    "attributes": {
                        "title": "Lesson's second VIDEO",
                        "user_id": "@variableType(integer)",
                        "lesson_id": "@variableType(integer)",
                        "link": "http:\/\/example2.com",
                        "description": "This is a second video",
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
           "title":"Lesson's second VIDEO UPDATED",
           "description":"This is a second video UPDATED",
           "link": "http:\/\/example2-updated.com"
        }
        """
        When I send request to "/videos/2" using HTTP PATCH
        And the response code is 200
        And the response body contains JSON:
        """
       {
            "data": {
                "title":"Lesson's second VIDEO UPDATED",
                "description":"This is a second video UPDATED",
                "link": "http:\/\/example2-updated.com"
            }
        }
        """

        When I send request to "/videos/2" using HTTP GET
        And the response code is 200
        And the response body contains JSON:
        """
        {
            "data": {
                "id": 2,
                "title": "Lesson's second VIDEO UPDATED",
                "description": "This is a second video UPDATED",
                "link": "http:\/\/example2-updated.com",
                "user_id": "@variableType(integer)",
                "lesson_id": "@variableType(integer)",
                "created_at": "@variableType(string)",
                "updated_at": "@variableType(string)"
            }
        }
        """

        When I send request to "/videos/1" using HTTP GET
        And the response code is 200
        And the response body contains JSON:
        """
        {
            "data": {
                "id": 1,
                "title": "Lesson's first VIDEO",
                "link": "http:\/\/example.com",
                "description": "This is a first video",
                "user_id": "@variableType(integer)",
                "lesson_id": "@variableType(integer)",
                "created_at": "@variableType(string)",
                "updated_at": "@variableType(string)"
            }
        }
        """

        Then the request body is:
        """
        {
           "title": "Lesson's second VIDEO UPDATED",
           "link":"test",
           "description":"aapkwekferf';elg[r]3lg3[lg3mf34nfi3n4nnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnn23423423423p[kp23k[23k4p[23k4p[234p[23k"
        }
        """
        When I send request to "/videos/1" using HTTP PATCH
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
                ],
                "link": [
                    "The link format is invalid."
                ]
            }
        }
        """

    Scenario: Update video with invalid ID
        Then the request body is:
        """
        {
           "title": "Test video title 2"
        }
        """
        When I send request to "/videos/INVALID-ID" using HTTP PATCH
        And the response code is 404
        And the response body contains JSON:
        """
        {
           "code": 404,
           "message": "Video with ID INVALID-ID is not found"
        }
        """
