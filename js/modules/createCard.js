import { imgTagReplace } from './imgTagReplace.js';

export const createCard = (template, cardData) => {
  const newCardEl = template.cloneNode(true);
  newCardEl.id = `${cardData.card_id}-card`;
  // newCardEl.style.backgroundColor = cardData.bg_colour;
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
  if (cardData.card_id == 'gigs') {
    if (!cardData.content || cardData.content.length === 0) {
      const nothing = document.createElement('p');
      nothing.innerHTML = 'No gigs currently in the diary';
      contentEl.appendChild(nothing);
    } else {
      const headings = ['date', 'venue', 'city'];
      const table = document.getElementById('gigTable');
      const hRow = document.getElementById('gigHead');
      const row = document.getElementById('gigRow');
      const hCell = hRow.firstElementChild;
      const cell = row.firstElementChild;
      hRow.innerHTML = '';
      for (let heading of headings) {
        const thisHeaderCell = hCell.cloneNode();
        thisHeaderCell.innerHTML = heading;
        hRow.appendChild(thisHeaderCell);
      }
      table.appendChild(hRow);
      table.classList.remove('hidden');
      for (let gig of cardData.content) {
        let thisRow = row.cloneNode();
        thisRow.id = gig.date;
        for (let heading of headings) {
          const thisCell = cell.cloneNode();
          thisCell.innerHTML = gig[heading];
          thisRow.appendChild(thisCell);
        }
        table.appendChild(thisRow);
      }
      contentEl.appendChild(table);
    }
  }
  if (cardData.content && cardData.card_id != 'gigs') {
    cardData.content.forEach((article) => {
      const thisArticleEl = articleEl.cloneNode();
      let dateString = '';
      if (cardData.content_type.substr(0, 5) === 'multi') {
        dateString = `<div class = "article-date">${article.live_date}</div>`;
      }
      article.article_content = imgTagReplace(article);
      const contentArray = article.article_content.split('\n');
      thisArticleEl.innerHTML = `${dateString}<p>${contentArray.join('</p>\n\t\t<p>')}</p>`;
      contentEl.appendChild(thisArticleEl);
    });
  }
  return newCardEl;
};
