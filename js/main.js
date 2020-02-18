/**
 * update modal with Link
 * @param {object} event
 */
function updateLink(event) {
    switch (event.target.id) {
        case "getTask":
            document.getElementById("codeSection").innerHTML = '';
            updateMainModelContent("https://restapi.gopibabu.live/v1/tasks/{number}");
            break;
        case "deleteTask":
            document.getElementById("codeSection").innerHTML = '';
            updateMainModelContent("https://restapi.gopibabu.live/v1/tasks/{number}");
            break;
        case "completedTasks":
            document.getElementById("codeSection").innerHTML = '';
            updateMainModelContent("https://restapi.gopibabu.live/v1/tasks/complete");
            break;
        case "incompletedTasks":
            document.getElementById("codeSection").innerHTML = '';
            updateMainModelContent(
                "https://restapi.gopibabu.live/v1/tasks/incomplete"
            );
            break;
        case "allTasks":
            document.getElementById("codeSection").innerHTML = '';
            updateMainModelContent("https://restapi.gopibabu.live/v1/tasks");
            break;
        case "tasksByPageNumber":
            document.getElementById("codeSection").innerHTML = '';
            updateMainModelContent(
                "https://restapi.gopibabu.live/v1/tasks/page/{number}"
            );
            break;
        case "createNewTask":
            document.getElementById("codeSection").innerHTML = '';
            updateMainModelContent("https://restapi.gopibabu.live/v1/tasks");
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
            document.getElementById("codeSection").innerHTML = '';
            updateMainModelContent("https://restapi.gopibabu.live/v1/tasks/{number}");
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
            document.getElementById("codeSection").innerHTML = '';
            updateMainModelContent("https://restapi.gopibabu.live/v1/users");
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
            document.getElementById("codeSection").innerHTML = '';
            updateMainModelContent("https://restapi.gopibabu.live/v1/sessions");
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
            updateMainModelContent("API is not yet designed!!");
    }
}

/**
 * Update modal text with content
 * @param {string} api
 */
function updateMainModelContent(api) {
    document.getElementById("mainModalContent").innerHTML = api;
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
