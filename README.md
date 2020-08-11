![REST API-PHP](./images/banner.png)

> This project focuses on implementing Restful web services using simple **vanilla PHP**.

## Scenario

- Build a task list system that will allow users to log in and create, update and delete tasks.
- Each user’s tasks will be private to them and other users will not be able to view them.
- We are responsible for database backend, the web services and Authentication module.
- We are not responsible for the front end or the server setup.

## API Requirements - Tasks

- Return JSON response for all APIs and allow caching where appropriate
- A task should have an ID, title, description, deadline date, completion status
- Return a list of details for all tasks for a user using a URL of: **/tasks**
- Return a list of details for all tasks for a user with pagination using a URL of : **/tasks/page/{:page}**
- Return a list of details for all tasks for a single task for a user using a URL of : **/tasks/{:taskid}**
- Return a list of details for all incomplete tasks for a user using URL of: **/tasks/incomplete**
- Return a list of details for all complete tasks for a user using URL of: **/tasks/complete**
- Delete a task for a user using URL of: **/tasks/{:taskid}**
- Update title, description, deadline date or completion status and return updated task using a URL of : **/tasks/{:taskid}**
- Create a task and return the details for the new task using a URL of : **/tasks**

## API Requirements - Authentication

- Return a JSON response for all APIs
- A user has an ID, full name, unique username, hashed password, user active status and login attempts
- A user can login in on more than one device and should not logout a previous device **(Sessions)**
- Create a new user using URL of : **/users**
- Login a user using a URL of : **/sessions**
- Logout a user using a URL of : **/sessions/{:sessionid}**
- Limited lifetime of a session access token, refreshed using a URL of : **/sessions/{:sessionid}**

[What is RESTful API](./REFERENCE.md)

## ✈TODO

- [ ] Add CORS (Cross-Origin Resource Sharing).
- [ ] Amending the API to allow you to upload files (images).
- [ ] Convert functional parts of the project into to OOPs.
