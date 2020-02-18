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
            break;
        case "deleteTask":
            updateMainModelContent("https://restapi.gopibabu.live/v1/tasks/{number}", title);
            break;
        case "completedTasks":
            updateMainModelContent("https://restapi.gopibabu.live/v1/tasks/complete", title);
            break;
        case "incompletedTasks":
            updateMainModelContent("https://restapi.gopibabu.live/v1/tasks/incomplete", title);
            break;
        case "allTasks":
            updateMainModelContent("https://restapi.gopibabu.live/v1/tasks", title);
            break;
        case "tasksByPageNumber":
            updateMainModelContent("https://restapi.gopibabu.live/v1/tasks/page/{number}", title);
            break;
        case "createNewTask":
            updateMainModelContent("https://restapi.gopibabu.live/v1/tasks", title);
            let createTaskCode = `
            <section class="m-3 p-3 border border-primary">
            <code id="modalCode">
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
            <code id="modalCode">
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
            <code id="modalCode">
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
            <code id="modalCode">
                    {<br>
                    "username" : "john",<br>
                    "password": "john12345",<br>
                    }<br>
                </code>
             </section>
            `;
            document.getElementById("codeSection").innerHTML = createSessionCode;
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
