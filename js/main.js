/**
 * update modal with Link
 * @param {object} event
 */
function updateLink(event) {
  console.log(event);
  switch (event.target.innerHTML.trim()) {
    case "GET TASK":
      updateMainModelContent("https://restapi.gopibabu.live/v1/tasks/{number}");
      break;
    case "DELETE TASK":
      updateMainModelContent("https://restapi.gopibabu.live/v1/tasks/{number}");
      break;
    case "GET ALL COMPLETED TASKS":
      updateMainModelContent("https://restapi.gopibabu.live/v1/tasks/complete");
      break;
    case "GET ALL INCOMPLETED TASKS":
      updateMainModelContent(
        "https://restapi.gopibabu.live/v1/tasks/incomplete"
      );
      break;
    case "GET ALL TASKS":
      updateMainModelContent("https://restapi.gopibabu.live/v1/tasks");
      break;
    case "GET ALL TASKS BY PAGE":
      updateMainModelContent(
        "https://restapi.gopibabu.live/v1/tasks/page/{number}"
      );
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
