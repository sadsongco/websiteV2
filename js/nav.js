const navigateTo = (id) => {
  document.getElementById(id).scrollIntoView({ behavior: 'smooth' });
  activateSelector(id);
};

const activateSelector = (id) => {
  window.history.pushState(null, '', `?page=${id}`);
  detectScroll = false;
  const cards = document.querySelectorAll('.card');
  for (const card of cards) {
    const selectorId = `${card.id}Selector`;
    const selector = document.getElementById(selectorId);
    if (card.id == id) {
      selector.classList.add('selected');
      selector.classList.remove('deselected');
      continue;
    }
    selector.classList.remove('selected');
    selector.classList.add('deselected');
  }
  setTimeout(() => (detectScroll = true), 700);
};

let cardsInitPos = {};
const cardPosTolerance = 15;
let detectScroll = false;
setTimeout(() => (detectScroll = true), 700);

const getCardsInitPos = () => {
  const cards = document.querySelectorAll('.card');
  for (const card of cards) {
    cardsInitPos[card.id] = card.offsetTop;
  }
  const params = new URLSearchParams(document.location.search);
  const page = params.get('page');
  if (page != '' && cardsInitPos.hasOwnProperty(page)) {
    document.getElementById(page).scrollIntoView({ behavior: 'smooth' });
    activateSelector(page);
  } else {
    detectScroll = false;
    window.scrollTo({ top: 0, left: 0, behavior: 'smooth' });
    setTimeout(() => (detectScroll = true), 700);
    return;
  }

  activateNearestSelector();
};

const activateNearestSelector = () => {
  if (!detectScroll) return;
  for (const [id, pos] of Object.entries(cardsInitPos)) {
    if (window.scrollY > pos - cardPosTolerance && window.scrollY < pos + cardPosTolerance) {
      activateSelector(id);
    }
  }
};

window.onscroll = () => {
  if (!detectScroll) return;
  activateNearestSelector();
};

window.onresize = () => {
  getCardsInitPos();
};
