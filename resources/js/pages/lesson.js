const iconLink = document.querySelectorAll('.link-icon');
const iconLesson = document.querySelector('.icon-lesson');


iconLink.forEach( element => {
  element.addEventListener('click', () => {
    element.parentNode.lastElementChild.classList.toggle('sub-menu--opened');
    iconLesson.classList.toggle('icon-lesson--opened');
  });
});