import { loadData } from './modules/loadData.js';
import { createCard } from './modules/createCard.js';
import { buildNav } from './nav.js';

// global content container
const contentContainer = document.getElementById('content-container');
// global card container
const cardContainer = document.getElementById('default');
// global content data
let contentData;

// get data from API
try {
  contentData = await loadData();
} catch {
  document.getElementById('initial-load').classList.remove('hidden');
}
// we have data, empty the content container
contentContainer.innerHTML = '';

// create cards
for (const cardData of contentData) {
  const cardEl = createCard(cardContainer, cardData);
  contentContainer.appendChild(cardEl);
  cardData.el = cardEl;
}

// build navigation menu from cards
buildNav();

// page complete, hide loading spinner
document.getElementById('initial-load').classList.add('hidden');
