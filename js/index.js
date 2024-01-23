const cardsRes = await fetch('/api/get-cards.php');
const cards = await cardsRes.text();
console.log(cards);
