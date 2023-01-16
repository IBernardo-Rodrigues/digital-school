import { showModal } from "../modules/modal.js";
import { cleanUrlQueryStrings } from "../modules/url.js";

const $btnCloseModal = document.querySelector('.btn-close-modal'); 

if ($btnCloseModal) $btnCloseModal.addEventListener('click', cleanUrlQueryStrings);

showModal("status", {
  created: document.querySelector('#modal-created'),
  deleted: document.querySelector('#modal-deleted')
});