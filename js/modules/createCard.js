import { imgTagReplace } from './imgTagReplace.js';

export const createCard = (template, cardData) => {
  const newCardEl = template.cloneNode(true);
  newCardEl.id = `${cardData.card_id}-card`;
  newCardEl.dataset.pos = cardData.card_pos;
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
      const headings = ['date', 'venue', 'city', 'address', 'country', 'tickets'];
      const table = document.getElementById('gigsTable');
      const tbody = document.getElementById('gigsBody');
      const hRow = document.getElementById('gigHeadRow');
      const row = document.getElementById('gigBodyRow');
      const hCell = document.getElementById('gigHeadCell');
      hCell.remove();
      const cell = document.getElementById('gigBodyCell');
      cell.remove();
      row.remove();
      for (let heading of headings) {
        const thisHeaderCell = hCell.cloneNode();
        thisHeaderCell.removeAttribute('id');
        thisHeaderCell.innerHTML = heading;
        hRow.appendChild(thisHeaderCell);
      }
      table.classList.remove('hidden');
      for (let gig of cardData.content) {
        let thisRow = row.cloneNode();
        thisRow.id = gig.date;
        for (let heading of headings) {
          const thisCell = cell.cloneNode();
          thisCell.removeAttribute('id');
          if (heading === 'tickets' && gig[heading] && gig[heading].substr(0, 4) === 'http') {
            gig[heading] = `<a href = '${gig[heading]}' target='_blank'>BUY TICKETS</a>`;
          }
          if (heading === 'address') {
            const mapURL = '<a href = "' + encodeURI(`https://www.google.com/maps/search/?api=1&query=${gig.venue}, ${gig.address}, ${gig.city} ${gig.postcode ? gig.postcode : ''} ${gig.country}`) + '" target = "_blank">' + `${gig.address} ${gig.postcode ? gig.postcode : ''}` + '</a>';
            gig[heading] = mapURL;
          }
          if (heading === 'venue' && gig['website'] != '') {
            gig[heading] = `<a href = '${gig['website']}' target='_blank'>${gig['venue']}</a>`;
          }
          if (gig[heading] != '') thisCell.innerHTML = gig[heading];
          thisCell.classList.add(heading);
          thisRow.appendChild(thisCell);
        }
        tbody.appendChild(thisRow);
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
