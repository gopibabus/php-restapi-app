/**
 * update modal with Link
 * @param {object} event
 */
function updateLink(event) {
    let title = event.target.innerText;
    document.getElementById("codeSection").innerHTML = '';
    switch (event.target.id) {
        case "getTask":
            updateMainModelContent("https://restapi.gopibabu.live/v1/tasks/{number}", title);
            let getTask = `
            <section class="m-3 p-3 border border-primary">
                    <span class="font-weight-bolder">Headers</span> 
                    <p class="card card-text mt-3 p-3">
                    <span class="text-danger">Authorization:</span><br>
                        Njc0MDQ1ODYyODk5MzhmMWU5YTZjMjQ2MTc2ZjIyZjRmMWQ5MDA1N2RkNmY5NDIyMTU4MjEyNzM1OA==
                    </p>
             </section>
            `;
            document.getElementById("codeSection").innerHTML = getTask;
            break;
        case "deleteTask":
            updateMainModelContent("https://restapi.gopibabu.live/v1/tasks/{number}", title);
            let deleteTask = `
            <section class="m-3 p-3 border border-primary">
                    <span class="font-weight-bolder">Headers</span> 
                    <p class="card card-text mt-3 p-3">
                    <span class="text-danger">Authorization:</span><br>
                        Njc0MDQ1ODYyODk5MzhmMWU5YTZjMjQ2MTc2ZjIyZjRmMWQ5MDA1N2RkNmY5NDIyMTU4MjEyNzM1OA==
                    </p>
             </section>
            `;
            document.getElementById("codeSection").innerHTML = deleteTask;
            break;
        case "completedTasks":
            updateMainModelContent("https://restapi.gopibabu.live/v1/tasks/complete", title);
            let completedTasks = `
            <section class="m-3 p-3 border border-primary">
                    <span class="font-weight-bolder">Headers</span> 
                    <p class="card card-text mt-3 p-3">
                    <span class="text-danger">Authorization:</span><br>
                        Njc0MDQ1ODYyODk5MzhmMWU5YTZjMjQ2MTc2ZjIyZjRmMWQ5MDA1N2RkNmY5NDIyMTU4MjEyNzM1OA==
                    </p>
             </section>
            `;
            document.getElementById("codeSection").innerHTML = completedTasks;
            break;
        case "incompletedTasks":
            updateMainModelContent("https://restapi.gopibabu.live/v1/tasks/incomplete", title);
            let incompletedTasks = `
            <section class="m-3 p-3 border border-primary">
                    <span class="font-weight-bolder">Headers</span> 
                    <p class="card card-text mt-3 p-3">
                    <span class="text-danger">Authorization:</span><br>
                        Njc0MDQ1ODYyODk5MzhmMWU5YTZjMjQ2MTc2ZjIyZjRmMWQ5MDA1N2RkNmY5NDIyMTU4MjEyNzM1OA==
                    </p>
             </section>
            `;
            document.getElementById("codeSection").innerHTML = incompletedTasks;
            break;
        case "allTasks":
            updateMainModelContent("https://restapi.gopibabu.live/v1/tasks", title);
            let allTasks = `
            <section class="m-3 p-3 border border-primary">
                    <span class="font-weight-bolder">Headers</span> 
                    <p class="card card-text mt-3 p-3">
                    <span class="text-danger">Authorization:</span><br>
                        Njc0MDQ1ODYyODk5MzhmMWU5YTZjMjQ2MTc2ZjIyZjRmMWQ5MDA1N2RkNmY5NDIyMTU4MjEyNzM1OA==
                    </p>
             </section>
            `;
            document.getElementById("codeSection").innerHTML = allTasks;
            break;
        case "tasksByPageNumber":
            updateMainModelContent("https://restapi.gopibabu.live/v1/tasks/page/{number}", title);
            let tasksByPageNumber = `
            <section class="m-3 p-3 border border-primary">
                    <span class="font-weight-bolder">Headers</span> 
                    <p class="card card-text mt-3 p-3">
                    <span class="text-danger">Authorization:</span><br>
                        Njc0MDQ1ODYyODk5MzhmMWU5YTZjMjQ2MTc2ZjIyZjRmMWQ5MDA1N2RkNmY5NDIyMTU4MjEyNzM1OA==
                    </p>
             </section>
            `;
            document.getElementById("codeSection").innerHTML = tasksByPageNumber;
            break;
        case "createNewTask":
            updateMainModelContent("https://restapi.gopibabu.live/v1/tasks", title);
            let createTaskCode = `
            <section class="m-3 p-3 border border-primary">
            <span class="font-weight-bolder">Headers</span> 
            <p class="card card-text mt-3 p-3">
            <span class="text-danger mt-3">Content-Type:</span><br>
                application/json
            <span class="text-danger mt-3">Authorization:</span><br>
            Njc0MDQ1ODYyODk5MzhmMWU5YTZjMjQ2MTc2ZjIyZjRmMWQ5MDA1N2RkNmY5NDIyMTU4MjEyNzM1OA==
            </p>
            <span class="font-weight-bolder">Request Body</span>
            <code id="modalCode" class="card card-text p-3 mt-3">
                    {<br>
                    "title" : "New Title",<br>
                    "completed" : "Y",<br>
                    "description": "New task description",<br>
                    "deadline" : "09/02/2020 13:00"<br>
                    }<br>
                </code>
             </section>
            `;
            document.getElementById("codeSection").innerHTML = createTaskCode;
            break;
        case "updateTask":
            updateMainModelContent("https://restapi.gopibabu.live/v1/tasks/{number}", title);
            let updateTaskCode = `
            <section class="m-3 p-3 border border-primary">
            <span class="font-weight-bolder">Headers</span> 
            <p class="card card-text mt-3 p-3">
            <span class="text-danger mt-3">Content-Type:</span><br>
                application/json
            <span class="text-danger mt-3">Authorization:</span><br>
            Njc0MDQ1ODYyODk5MzhmMWU5YTZjMjQ2MTc2ZjIyZjRmMWQ5MDA1N2RkNmY5NDIyMTU4MjEyNzM1OA==
            </p>
            <span class="font-weight-bolder">Request Body</span>
            <code id="modalCode" class="card card-text p-3 mt-3">
                    {<br>
                    "title" : "New Title",<br>
                    "completed" : "Y",<br>
                    "description": "New task description",<br>
                    "deadline" : "09/02/2020 13:00"<br>
                    }<br>
                </code>
             </section>
            `;
            document.getElementById("codeSection").innerHTML = updateTaskCode;
            break;
        case "createUser":
            updateMainModelContent("https://restapi.gopibabu.live/v1/users", title);
            let createUserCode = `
            <section class="m-3 p-3 border border-primary">
            <span class="font-weight-bolder">Headers</span> 
            <p class="card card-text mt-3 p-3">
            <span class="text-danger mt-3">Content-Type:</span><br>
                application/json
            </p>
            <span class="font-weight-bolder">Request Body</span>
            <code id="modalCode" class="card card-text p-3 mt-3">
                    {<br>
                    "fullname" : "New Title",<br>
                    "username" : "john",<br>
                    "password": "john12345",<br>
                    }<br>
                </code>
             </section>
            `;
            document.getElementById("codeSection").innerHTML = createUserCode;
            break;
        case "createSession":
            updateMainModelContent("https://restapi.gopibabu.live/v1/sessions", title);
            let createSessionCode = `
            <section class="m-3 p-3 border border-primary">
            <span class="font-weight-bolder">Headers</span> 
            <p class="card card-text mt-3 p-3">
            <span class="text-danger mt-3">Content-Type:</span><br>
                application/json
            </p>
            <span class="font-weight-bolder">Request Body</span>
            <code id="modalCode" class="card card-text p-3 mt-3">
                    {<br>
                    "username" : "john",<br>
                    "password": "john12345",<br>
                    }<br>
                </code>
             </section>
            `;
            document.getElementById("codeSection").innerHTML = createSessionCode;
            break;
        case "destroySession":
            updateMainModelContent("https://restapi.gopibabu.live/v1/sessions/{sessionid}", title);
            let destroySessionCode = `
            <section class="m-3 p-3 border border-primary">
                    <span class="font-weight-bolder">Headers</span> 
                    <p class="card card-text mt-3 p-3">
                    <span class="text-danger">Authorization:</span><br>
                        Njc0MDQ1ODYyODk5MzhmMWU5YTZjMjQ2MTc2ZjIyZjRmMWQ5MDA1N2RkNmY5NDIyMTU4MjEyNzM1OA==
                    </p>
             </section>
            `;
            document.getElementById("codeSection").innerHTML = destroySessionCode;
            break;
        case "renewSession":
            updateMainModelContent("https://restapi.gopibabu.live/v1/sessions/{sessionid}", title);
            let renewSessionCode = `
            <section class="m-3 p-3 border border-primary">
            <span class="font-weight-bolder">Headers</span> 
                    <p class="card card-text mt-3 p-3">
                    <span class="text-danger">Authorization:</span><br>
                        Njc0MDQ1ODYyODk5MzhmMWU5YTZjMjQ2MTc2ZjIyZjRmMWQ5MDA1N2RkNmY5NDIyMTU4MjEyNzM1OA==
                    <span class="text-danger mt-3">Content-Type:</span><br>
                        application/json
                    </p>
            <span class="font-weight-bolder">Request Body</span>
            <code id="modalCode" class="card card-text p-3 mt-3">
                    {<br>
                    "refresh_token" : "Njc0MDQ1ODYyODk",<br>
                    }<br>
                </code>
             </section>
            `;
            document.getElementById("codeSection").innerHTML = renewSessionCode;
            break;
        default:
            updateMainModelContent("API is not yet designed!!", title);
    }
}

/**
 * Update modal text and title with content
 * @param {string} api
 * @param {string} title
 */
function updateMainModelContent(api, title) {
    document.getElementById("mainModalContent").innerHTML = api;
    document.getElementById("mainModalLabel").innerHTML = title;
}

document.getElementById("getTask").addEventListener("click", updateLink);
document.getElementById("deleteTask").addEventListener("click", updateLink);
document.getElementById("completedTasks").addEventListener("click", updateLink);
document.getElementById("incompletedTasks").addEventListener("click", updateLink);
document.getElementById("allTasks").addEventListener("click", updateLink);
document.getElementById("tasksByPageNumber").addEventListener("click", updateLink);
document.getElementById("createNewTask").addEventListener("click", updateLink);
document.getElementById("updateTask").addEventListener("click", updateLink);
document.getElementById("createUser").addEventListener("click", updateLink);
document.getElementById("createSession").addEventListener("click", updateLink);
document.getElementById("destroySession").addEventListener("click", updateLink);
document.getElementById("renewSession").addEventListener("click", updateLink);
