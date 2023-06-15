import { insertMessage } from './messages.js';

export const closeOpenModals = () => {
  const openModals = document.getElementsByClassName('modal-open');
  for (let openModal of openModals) openModal.classList.remove('modal-open');
};

export const highlightErrors = (errorFields, form) => {
  const errorMessage = document.createElement('h1');
  errorMessage.innerHTML = 'You missed out some information';
  insertMessage('You missed out some information', form);
  errorFields.forEach((errorField) => {
    document.getElementById(errorField).classList.add('error');
  });
};

export const createDiv = (className = '', contents = '') => {
  const div = document.createElement('div');
  div.classList.add(className);
  if (typeof contents === 'object') div.appendChild(contents);
  else div.innerHTML = contents;
  return div;
};
