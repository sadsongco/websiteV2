import { Sortable } from '../../lib/sortable.core.esm.js';
import { editCard } from './modules/editCard.js';

const loading = document.getElementById('loading');
window.onload = () => {
  loading.classList.add('hidden');
  loading.addEventListener('transitionend', hideLoading());
};

const hideLoading = () => {
  loading.style.display = 'none';
};

const updateOrder = async () => {
  loading.style.display = 'flex';
  loading.classList.remove('hidden');
  const cards = document.getElementById('cardsPreview').getElementsByTagName('li');
  let getArr = [];
  for (const card_pos in cards) {
    if (!cards[card_pos].id) continue;
    getArr.push(`${cards[card_pos].id}=${card_pos}`);
  }
  const getString = getArr.join('&');
  const updateOrderURL = './API/updateOrder.php?' + getString;
  try {
    await fetch(updateOrderURL);
  } catch (err) {
    console.error('API error: ', err);
  }
  loading.classList.add('hidden');
};

const getCardInfo = async () => {
  try {
    const response = await fetch('./API/cards.php');
    return response.json();
  } catch (err) {
    console.error('API error: ', err);
  }
};

let cardInfo = await getCardInfo();
let cardsPreview = document.getElementById('cardsPreview');
cardsPreview.classList.add('slist');
let cardTemplate = document.getElementById('cardPreview');
cardTemplate.remove();
cardTemplate.style.width = 100 / cardInfo.length + '%';
for (let card of cardInfo) {
  const cardEl = cardTemplate.cloneNode();
  cardEl.id = card.card_id;
  cardEl.innerHTML = card.card_id;
  cardEl.dataset.pos = card.card_pos;
  cardEl.addEventListener('dblclick', (e) => {
    editCard(cardInfo[e.target.dataset.pos]);
  });
  cardsPreview.appendChild(cardEl);
}

const sortable = Sortable.create(cardsPreview, {
  animation: 300,
  onUpdate: (evt) => {
    updateOrder();
  },
});
