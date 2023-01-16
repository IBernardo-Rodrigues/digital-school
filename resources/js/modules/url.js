export function cleanUrlQueryStrings() {
  const url = location.href;
  const queryParams = location.search;
  
  let cleanedUrl = url.replace(queryParams, "");
  history.replaceState({}, "", cleanedUrl);
}

export function getQueryStrings() {
  let queryParams = window.location.search;
  queryParams = queryParams.split(/[?&]/g);
  queryParams.shift();

  return queryParams;
}