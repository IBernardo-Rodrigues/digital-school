import { showModal } from "../modules/modal.js";
import { cleanUrlQueryStrings } from "../modules/url.js";

const $btnDelete = document.querySelectorAll('.btn-delete');
const $btnCloseModal = document.querySelector('.btn-close-modal'); 

if ($btnCloseModal) $btnCloseModal.addEventListener('click', cleanUrlQueryStrings);

$btnDelete.forEach( element => {
  element.addEventListener('click', () => {
    const lessonId = element.dataset.id;
    setDeleteLink(lessonId);
  });
});

function setDeleteLink(lessonId) {
  let $linkField = document.querySelector("#modal-confirm-delete a");

  const url = $linkField.dataset.url;
  const newPath = `${url}/admin/users/${lessonId}/delete`;

  $linkField.href = newPath;
}

showModal("status", {
  created: document.querySelector('#modal-created'),
  deleted: document.querySelector('#modal-deleted')
});