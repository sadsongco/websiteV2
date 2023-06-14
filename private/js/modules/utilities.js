export const closeOpenModals = () => {
  const openModals = document.getElementsByClassName('modal-open');
  for (let openModal of openModals) openModal.classList.remove('modal-open');
};
