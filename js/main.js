/**
 * update modal with Link
 * @param {object} event
 */
function updateLink(event) {
  switch (event.toElement.id) {
    case "getTask":
      updateMainModelContent("https://restapi.gopibabu.live/v1/tasks/{number}");
      break;
    case "deleteTask":
      updateMainModelContent("https://restapi.gopibabu.live/v1/tasks/{number}");
      break;
    case "completedTasks":
      updateMainModelContent("https://restapi.gopibabu.live/v1/tasks/complete");
      break;
    case "incompletedTasks":
      updateMainModelContent(
        "https://restapi.gopibabu.live/v1/tasks/incomplete"
      );
      break;
    case "allTasks":
      updateMainModelContent("https://restapi.gopibabu.live/v1/tasks");
      break;
    case "tasksByPageNumber":
      updateMainModelContent(
        "https://restapi.gopibabu.live/v1/tasks/page/{number}"
      );
      break;
    case "createNewTask":
      updateMainModelContent("https://restapi.gopibabu.live/v1/tasks");
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
document
  .getElementById("incompletedTasks")
  .addEventListener("click", updateLink);
document.getElementById("allTasks").addEventListener("click", updateLink);
document
  .getElementById("tasksByPageNumber")
  .addEventListener("click", updateLink);
document.getElementById("createNewTask").addEventListener("click", updateLink);
