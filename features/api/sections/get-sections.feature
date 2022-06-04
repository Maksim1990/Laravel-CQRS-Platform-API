@get-sections
Feature: Get sections
    In order to use application
    As an application user
    I need to be able to get sections

    Background:
        Given the "Content-Type" request header is "application/json"
        Then I get behat token
        And I save it into "behatToken"
        Given the "Authorization" request header is "Bearer <<behatToken>>"

    Scenario: Get all sections and by specific ID when no sections exist
        When I send request to "/sections" using HTTP GET
        And the response code is 200
        And the response body contains JSON:
        """
        {
         "data": []
        }
        """

        When I send request to "/sections/INVALID-ID" using HTTP GET
        And the response code is 404
        And the response body contains JSON:
        """
        {
           "code": 404,
           "message": "Section with ID INVALID-ID is not found"
        }
        """

    Scenario: Get all sections and by specific ID
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

        Then the request body is:
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

        When I send request to "/sections" using HTTP GET
        And the response code is 200
        And the response body contains JSON:
        """
        {
          "data": [
                 {
                    "type": "sections",
                    "id": 1,
                    "attributes": {
                        "name": "Test section name",
                        "description": "Test section description",
                        "course_id": "@variableType(integer)",
                        "user_id": "@variableType(integer)",
                        "created_at": "@variableType(string)",
                        "updated_at": "@variableType(string)"
                    }
                }
           ]
        }
        """

        # Add lesson relation
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

        When I send request to "/sections/1?relationships=course;lessons;user" using HTTP GET
        And the response code is 200
        And the response body contains JSON:
        """
        {
            "data": {
                "id": 1,
                "name": "Test section name",
                "description": "Test section description",
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
                   "lessons": {
                        "data": [
                            {
                                "id": 1,
                                "name": "Test lesson name",
                                "section_id": 1,
                                "description": "Test lesson description",
                                "created_at": "@variableType(string)",
                                "updated_at": "@variableType(string)"
                            }
                        ]
                   }
               }
            }
        }
        """
