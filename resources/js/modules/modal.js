import { getQueryStrings } from "./url.js";

export function showModal(queryName, modals) {
  const queryParams = getQueryStrings();

  queryParams.forEach(element => {
    if (element.match(`${queryName}=`)) {
      const splittedQuery = element.split("=");
      const queryValue = splittedQuery[1];

      if (queryValue == "error") {
        const $myModal = getModalError();

        $myModal.show();
        return;
      }

      const status = Object.keys(modals);
      status.forEach( element => {
               
        if (element == queryValue) {
          const $modal = modals[element];
          const myModal = new bootstrap.Modal($modal);
          myModal.show();
        }
      });

    }
  });
}

function getModalError() {
  const $modalError = document.querySelector("#modal-error");
  let myModal = new bootstrap.Modal($modalError);
  const queryParams = getQueryStrings();

  queryParams.forEach( element => {
    if (element.match("error=")) {
      const splittedQuery = element.split("=");
      const queryValue = splittedQuery[1];

      let decodedURI = decodeURIComponent(queryValue);
      decodedURI = decodeURIComponent(decodedURI);
      
      const $modalErrorMessageField = document.querySelector('#modal-error p');
      $modalErrorMessageField.textContent = decodedURI;
    }
  });

  return myModal;
}