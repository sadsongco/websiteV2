import { createEditCardForm, createEditCardFormInputs, createEditCardFormSubmit } from './editCardForm.js';
import { createNewArticleForm, createArticleEls } from './editArticles.js';
import { createGigEditView } from './editGigs.js';

const loading = document.getElementById('loading');

export const editCard = async (card) => {
  loading.style.display = 'flex';
  loading.classList.remove('hidden');

  // container for card editing
  const container = document.getElementById('editCard');
  container.innerHTML = '';

  // container heading
  const editCardHeading = document.createElement('h1');
  editCardHeading.innerHTML = 'edit card info';
  container.appendChild(editCardHeading);

  // card edit form
  const form = createEditCardForm();
  createEditCardFormInputs(form, card);
  createEditCardFormSubmit(form);
  container.appendChild(form);

  if (card.card_id == 'gigs') {
    const gigEditView = await createGigEditView(card);
    container.appendChild(gigEditView);
  } else {
    // new article form
    const inputs = [
      {
        type: 'textarea',
        id: 'articleContent',
        name: 'article_content',
        placeholder: 'Enter your article content here',
        label: {
          for: 'articleContent',
          text: 'create new article',
        },
      },
      {
        type: 'datetime-local',
        id: 'liveDate',
        name: 'live_date',
        label: { for: 'liveDate', text: 'enter date for article to go live (if nothing entered will go live immediately)' },
      },
      { type: 'hidden', name: 'card_id', value: card.card_id },
      { type: 'submit', name: 'submit', value: 'upload new article' },
    ];
    const newArticleForm = createNewArticleForm(card, { id: 'newArticle', method: 'post', action: '#' }, inputs);
    container.appendChild(newArticleForm);

    // edit old articles
    const articlesEl = await createArticleEls(card);
    if (articlesEl) container.appendChild(articlesEl);
  }

  // hide loading
  loading.classList.add('hidden');
  loading.style.display = 'none';
};
