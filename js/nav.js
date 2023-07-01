export const buildNav = () => {
  let firstCard = null;
  const container = document.getElementById('content-container');

  const cardList = document.getElementsByClassName('card');
  const navUl = document.getElementById('nav-bar');
  const navToggle = document.getElementById('menu');
  for (const card of cardList) {
    if (firstCard === null) firstCard = card;
    container.addEventListener('scrollend', (e) => {
      firstCard = card;
    });
    const navLi = document.createElement('li');
    navLi.innerHTML = card.id.split('-')[0];
    navLi.onclick = () => {
      container.scroll({ top: 0, left: window.innerWidth * card.dataset.pos, behavior: 'smooth' });
      navToggle.checked = false;
    };
    navUl.appendChild(navLi);
  }
};
