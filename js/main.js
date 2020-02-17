/**
 * update modal with Link
 * @param {object} event
 */
function updateLink(event) {
  switch (event.target.innerHTML.trim()) {
    case "GET TASK":
      updateMainModelContent("https://restapi.gopibabu.live/v1/tasks/3");
      break;
    case "DELETE TASK":
      updateMainModelContent("https://restapi.gopibabu.live/v1/tasks/3");
      break;
    case "GET ALL COMPLETED TASKS":
      updateMainModelContent("https://restapi.gopibabu.live/v1/tasks/complete");
      break;
    case "GET ALL INCOMPLETED TASKS":
      updateMainModelContent("https://restapi.gopibabu.live/v1/tasks/incomplete");
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
