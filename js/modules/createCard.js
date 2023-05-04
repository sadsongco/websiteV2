export const createCard = (template, cardData) => {
  const newCardEl = template.cloneNode(true);
  newCardEl.id = `${cardData.card_id}-card`;
  newCardEl.style.backgroundColor = cardData.bg_colour;
  const titleEl = newCardEl.getElementsByClassName('card-title')[0];
  titleEl.id = `${cardData.card_id}-title`;
  titleEl.innerHTML = cardData.title;
  const contentEl = newCardEl.getElementsByClassName('card-content')[0];
  contentEl.id = `${cardData.card_id}-content`;
  const strap = contentEl.getElementsByClassName('card-strap')[0];
  strap.innerHTML = cardData.strap;
  strap.id = `${cardData.card_id}-strap`;
  const articleEl = contentEl.getElementsByClassName('card-article')[0];
  articleEl.remove();
  if (cardData.content) {
    const thisArticleEl = articleEl.cloneNode();
    const contentArray = cardData.content.article_content.split('\n');
    thisArticleEl.innerHTML = `<p>${contentArray.join('</p>\n\t\t<p>')}</p>`;
    contentEl.appendChild(thisArticleEl);
  }
  return newCardEl;
};
