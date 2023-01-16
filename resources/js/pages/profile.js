import { showModal } from "../modules/modal.js";
import { cleanUrlQueryStrings } from "../modules/url.js";

const $profileImgInput = document.querySelector('#profile-img');
const $btnUpdate = document.querySelector('.btn-update');
const $btnCloseModal = document.querySelector(".btn-close-modal");

$profileImgInput.addEventListener('change', (e) => {
  const $formProfile = $profileImgInput.parentElement;

  $formProfile.submit();
});

$btnUpdate.addEventListener('click', () => {
  const $form = document.querySelector('.profile-bottom form');

  $form.submit()
});

if ($btnCloseModal) $btnCloseModal.addEventListener('click', cleanUrlQueryStrings);

showModal("status", {
  updated: document.querySelector("#modal-updated"),
});
