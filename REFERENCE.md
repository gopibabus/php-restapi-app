# REFERENCE - RESTful API

## What is a RESTful API

- REST API is an interface that is stateless. It uses Client ↔ Server model via a Request ↔ Response architecture and utilize the standard HTTP verbs and status codes
- It is important to note that REST is not a standard, it is of principles that if an API was to follow would make it RESTful.
- REST is generally preferred over SOAP due to its simpler implementation and mostly uses JSON for response output.
- JSON is simpler and less verbose than XML which is what SOAP uses.

> **REST** : Representational State Transfer.
>
> **Stateless** : For each request the receiving system doesn’t know about any last requests
>
> **HTTP Verbs** : GET, POST, PATCH, DELETE to create, read, update and delete data(CRUD)

## HTTP Verbs

| HTTP Verb | Action |
| --------- | ------------- |
| POST      | Create        |
| GET       | Retrieve      |
| PATCH     | Update        |
| PUT       | Replace       |
| DELETE    | Delete        |

## Common HTTP Response Status Codes:

| Status Code | Meaning               |
| ----------- | --------------------- |
| 200         | OK                    |
| 201         | Created               |
| 400         | Bad Request           |
| 401         | Unauthorized          |
| 403         | Forbidden             |
| 404         | Not Found             |
| 405         | Method Not allowed    |
| 409         | Conflict              |
| 500         | Internal Server Error |

[FULL LIST OF HTTP STATUS CODES](https://httpstatuses.com)

## How RESTful WebServices implemented

| HTTP Verb                        | URL                                   | Explanation                                                        |
| -------------------------------- | ------------------------------------- | ------------------------------------------------------------------ |
| GET                              | https://api.mysite.com/products       | This would return list of all products                             |
| GET                              | https://api.mysite.com/products/{:id} | This would return one product that has ID of what ever you pass in |
| POST<br>along with <br>json body | https://api.mysite.com/products       | create a new product and return as a response to the request.      |

> It is best practice to have endpoints as plurals. Since it doesn’t matter if you are retrieving one user or many users, the endpoints would be consistent, but if you just wanted 1 user then you would add the user ID at the end of the route

> Never use routes like /getUsers, /createUser, /deleteUser - these don’t follow REST principles as you are mixing verbs with nouns(getUser). Keep the verbs for use in the HTTP request(GET, POST, PUT, PATCH) and noun as part of route(/users).

## What is Token Based Authentication ?

- A token is like a password with a limited life span.

- When a user authenticates with a username and password theyt are given 2 tokens: an access token and a refresh token.

- An access token has a really short lifespan(usually minutes or hours)
- A refresh token is valid for a lot longer (usually weeks or months)
- Both tokens are usually just a random set of base64 encoded characters

```
example: KUPDfxgdsWef2kJVjtGss1X6ra5UA==
```

- A random base64 encoded string is sent in the HTTP headerr and this is used as a password to authenticate you for every request.

- When a access token expires, you then use the refresh token to get a new access token (and a accompanying new refrsh token).

- The reason why a refresh token has longer lifespan is because it is only ever sent with a request to get a new access token. So it is less likely to be leaked or exposed to a potential hacker.

- We use sessions so we can use the system from multiple devices at the same time.

![autherization workflow](https://restapi.gopibabu.live/images/authentication_workflow.png)
