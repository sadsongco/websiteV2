import { loadData } from './modules/loadData.js';
import { createCard } from './modules/createCard.js';
import { loadBackgroundImage } from './modules/loadBackgroundImage.js';
import { placeBackgroundImages } from './modules/placeBackgroundImage.js';
import { animateOnScroll } from './animate-on-scroll.js';

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

// load images, create elements
const backgroundImagePromises = [];
for (const cardData of contentData) {
  // cardData.image = await loadBackgroundImage(cardData.bg_image);
  const cardEl = createCard(cardContainer, cardData);
  contentContainer.appendChild(cardEl);
  cardData.el = cardEl;
}

// try {
//   await Promise.all(backgroundImagePromises);
// } catch (e) {
//   console.log(`Couldn't load images: ${e}`);
// }
// images loaded, update cards with images
// await placeBackgroundImages(contentData);

// observe for animate on scroll
animateOnScroll();

// page complete, hide loading spinner
document.getElementById('initial-load').classList.add('hidden');
