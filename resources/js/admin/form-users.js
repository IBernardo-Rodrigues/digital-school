import { showModal } from "../modules/modal.js";
import { cleanUrlQueryStrings } from "../modules/url.js";

const $btnCloseModal = document.querySelector(".btn-close-modal");

//Listeners
if ($btnCloseModal) $btnCloseModal.addEventListener('click', cleanUrlQueryStrings);

// Application
showModal("status", {
  updated: document.querySelector("#modal-updated"),
});

