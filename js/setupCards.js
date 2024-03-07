const cardsPreview = document.getElementById('cardsPreview');

const updateOrder = async () => {
  const cards = document.getElementById('cardsPreview').getElementsByTagName('li');
  let getArr = [];
  for (const card_pos in cards) {
    if (!cards[card_pos].id) continue;
    getArr.push(`${cards[card_pos].id}=${card_pos}`);
  }
  const getString = getArr.join('&');
  const updateOrderURL = './api/update_order.php?' + getString;
  try {
    const res = await fetch(updateOrderURL);
    console.log(await res.text());
  } catch (err) {
    console.error('API error: ', err);
  }
};

const getCardInfo = async () => {
  try {
    const response = await fetch('./API/cards.php');
    return response.json();
  } catch (err) {
    console.error('API error: ', err);
  }
};

const sortable = Sortable.create(cardsPreview, {
  animation: 300,
  onUpdate: (evt) => {
    updateOrder();
  },
});
