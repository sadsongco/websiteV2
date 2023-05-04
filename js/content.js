// import { buildNav } from './nav.js';
// import { loadData } from './loadData.js';

// // grab loader element to use where needed
// const loader = document.getElementById('loading');

// // load background images and show page

// const loadBackgroundImage = async (src) => {
//   return new Promise((resolve, reject) => {
//     const image = new Image();
//     image.addEventListener('load', resolve);
//     image.addEventListener('error', reject);
//     image.src = src;
//     return image;
//   });
// };

// const placeBackgroundImage = async (src, target) => {
//   const imageEl = document.createElement('img');
//   imageEl.src = src;
//   imageEl.classList.add('bg-image');
//   target.appendChild(imageEl);
//   target.style.backgroundAttachment = 'fixed';
//   target.style.backgroundSize = 'cover';
//   target.style.backgroundImage = `url(${src})`;
// };

// const buildBackgrounds = async () => {
//   const backgrounds = {
//     about: 'https://picsum.photos/1000',
//     album: 'https://picsum.photos/800',
//     shows: 'https://picsum.photos/900',
//     other: 'https://picsum.photos/950',
//   };
//   const cards = document.getElementsByClassName('card');
//   let backgroundImagePromises = [];
//   for (const card of cards) {
//     backgroundImagePromises.push(loadBackgroundImage(backgrounds[card.id.replace('-container', '')]));
//   }
//   await Promise.all(backgroundImagePromises);
//   for (const backgroundKey in backgrounds) {
//     const target = document.getElementById(`${backgroundKey}-container`);
//     await placeBackgroundImage(backgrounds[backgroundKey], target);
//   }
//   document.getElementById('initial-load').style.display = 'none';
//   loader.style.display = 'none';
// };

// // load page content and display

// const showLoading = async (targetId) => {
//   const newLoader = loader.cloneNode(true);
//   newLoader.id = `${targetId}-loader`;
//   newLoader.style.display = 'inline-block';
//   const target = document.getElementById(`${targetId}-text`);
//   target.innerHTML = '';
//   target.appendChild(newLoader);
// };

// const updateContent = async (apiData) => {
//   for (let entry of apiData) {
//     const contentTarget = document.getElementById(`${entry.target}-text`);
//     contentTarget.innerHTML = entry.content.replace(/\n/g, '<br />');
//   }
// };

// const buildCard = async (card) => {
//   console.log(card);
//   const newCardEl = cardContainer.cloneNode(true);
//   newCardEl.id = card.card_id;
//   newCardEl.classList.add('hidden');
//   const titleEl = newCardEl.getElementsByClassName('card-title')[0];
//   titleEl.innerHTML = card.title;
//   const contentEl = newCardEl.getElementsByClassName('card-content')[0];
//   const text = contentEl.getElementsByClassName('card-strap')[0];
//   text.innerHTML = card.strap;
//   contentContainer.appendChild(newCardEl);
//   await loadBackgroundImage(card.bg_image);
//   await placeBackgroundImage(card.bg_image, newCardEl);
//   newCardEl.classList.remove('hidden');
// };

// const buildPage = async () => {
//   const data = await loadData();
//   if (data.length > 0) {
//     cardContainer.remove();
//   }
//   const cardPromises = [];
//   for (const card of data) {
//     cardPromises.push(buildCard(card));
//   }
//   console.log(cardPromises);
//   await Promise.all(cardPromises);
//   // buildNav();
//   // await showLoading('other');
//   // await showLoading('about');
//   // await showLoading('album');
//   // fetch('./API/home.php')
//   //   .then((res) => res.json())
//   //   .then((data) => updateContent(data));
//   document.getElementById('initial-load').classList.add('hidden');
// };

// // run the page
// // buildBackgrounds();
// await buildPage();

/** Start again */

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
  cardData.image = await loadBackgroundImage(cardData.bg_image);
  // backgroundImagePromises.push(loadBackgroundImage(cardData.bg_image));
  const cardEl = createCard(cardContainer, cardData);
  contentContainer.appendChild(cardEl);
  cardData.el = cardEl;
}

try {
  await Promise.all(backgroundImagePromises);
} catch (e) {
  console.log(`Couldn't load images: ${e}`);
}
// images loaded, update cards with images
await placeBackgroundImages(contentData);

// observe for animate on scroll
animateOnScroll();

// page complete, hide loading spinner
document.getElementById('initial-load').classList.add('hidden');
