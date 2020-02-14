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
document.getElementById("patchTask").addEventListener("click", updateLink);
