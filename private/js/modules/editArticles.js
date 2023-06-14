import { createInput, createLabel } from './createFormEls.js';
import { announceSuccess, insertMessage } from './messages.js';
import { closeOpenModals, highlightErrors } from './utilities.js';

// API calls
const submitNewArticle = async (target, form) => {
  const formData = new FormData();
  const errorFields = [];
  for (let el of form) {
    if (el.type != 'file' && el.name != 'live_date' && el.value == '') {
      errorFields.push(el.id);
      continue;
    }
    if (el.type == 'file') {
      formData.append(el.name, el.files[0]);
      continue;
    }
    formData.append(el.name, el.value);
  }
  if (errorFields.length > 0) return { success: false, error: 'validation', errorFields: errorFields };
  try {
    const res = await fetch(`./API/${target}Article.php`, {
      method: 'post',
      // headers: {
      //   'Content-Type': 'application/json',
      // },
      // body: JSON.stringify(data),
      body: formData,
    });
    // return await res.text();
    return await res.json();
  } catch (e) {
    return { success: false, error: 'API error: ' + e };
  }
};

const uploadImage = async (fileInput) => {
  const formData = new FormData();
  formData.append('image', fileInput.files[0]);
  try {
    const res = await fetch('./API/uploadArticleImage.php', {
      method: 'post',
      body: formData,
    });
    console.log(await res.text());
  } catch (e) {
    console.log(e);
  }
};

const getArticles = async (card) => {
  let content_type = card.content_type.substr(0, 5) === 'multi' ? 'multi' : 'single';
  const url = `./API/getArticles.php?card_id=${card.card_id}&content_type=${content_type}`;
  const articlesArr = await fetch(url);
  return articlesArr.json();
};

const editArticle = async (article, card) => {
  const inputs = [
    {
      type: 'textarea',
      id: 'articleContent',
      name: 'article_content',
      value: article.article_content,
      label: {
        for: 'articleContent',
        text: 'edit article',
      },
    },
    {
      type: 'datetime-local',
      id: 'liveDate',
      name: 'live_date',
      value: article.live_date,
      label: { for: 'liveDate', text: 'enter date for article to go live' },
    },
    { type: 'hidden', name: 'card_id', value: card.card_id },
    { name: 'article_id', type: 'hidden', value: article.article_id },
    { type: 'submit', name: 'update', value: 'update article' },
    { type: 'submit', name: 'delete', value: 'delete article' },
    { type: 'submit', name: 'cancel', value: 'cancel editing' },
  ];
  const form = createNewArticleForm({ id: 'editArticle', method: 'post', action: '#' }, inputs);
  const editArticleContainer = document.getElementById('editArticle');
  editArticleContainer.innerHTML = '';
  editArticleContainer.appendChild(form);
  editArticleContainer.classList.add('modal-open');
  editArticleContainer.scrollIntoView({ block: 'start', inline: 'start' });
};

export const createNewArticleForm = (formParams, inputs) => {
  const form = document.createElement('form');
  form.id = formParams.id;
  form.method = formParams.method;
  form.action = formParams.action;
  for (const input of inputs) {
    const inputEl = createInput(input);
    if (input.label) {
      const labelEl = createLabel(input.label);
      form.appendChild(labelEl);
    }
    form.appendChild(inputEl);

    // add event listener to process form
    if (input.type === 'submit') {
      inputEl.addEventListener('click', async (e) => {
        e.preventDefault();
        if (e.target.name == 'cancel') return closeOpenModals();
        if (e.target.name == 'delete' && !confirm('delete article - are you sure?')) return closeOpenModals();
        const res = await submitNewArticle(e.target.name, form);
        if (res.success) {
          document.getElementById('editCard').innerHTML = '';
          closeOpenModals();
          if (e.target.name == 'update') return announceSuccess('article updated');
          if (e.target.name == 'delete') return announceSuccess('article deleted');
          announceSuccess('new article submitted');
          return;
        }
        if (res.error === 'validation') return highlightErrors(res.errorFields, form.id);
        insertMessage('there was an error: ' + res.error, 'editCard');
      });
    }
  }

  return form;
};

export const createImageUpload = (idx, form) => {
  const imageUploadInput = document.createElement('input');
  imageUploadInput.name = `imageUpload_${idx}`;
  imageUploadInput.id = `imageUpload_${idx}`;
  imageUploadInput.type = 'file';
  imageUploadInput.addEventListener('input', (e) => {
    const [imageUploadInput, imageUploadLabel] = createImageUpload(++idx, form);
    form.appendChild(imageUploadLabel);
    form.appendChild(imageUploadInput);
  });
  const imageUploadLabel = document.createElement('label');
  imageUploadLabel.for = `imageUpload_${idx}`;
  imageUploadLabel.innerHTML = `upload an image for the article - paste <code>&lt;!--{{img-${idx}}}--&gt;</code> into the text`;
  return [imageUploadInput, imageUploadLabel];
  // const imageUploadSubmit = document.createElement('input');
  // imageUploadSubmit.type = 'submit';
  // imageUploadSubmit.value = 'upload article image';
  // imageUploadForm.appendChild(imageUploadSubmit);

  // // handle image upload when submitted
  // imageUploadSubmit.addEventListener('click', (e) => {
  //   e.preventDefault();
  //   uploadImage(imageUploadInput);
  // });
  return imageUploadForm;
};

const createArticleEl = async (article) => {
  const articleEl = document.createElement('section');
  articleEl.classList.add('article');
  const articleHead = document.createElement('h2');
  articleHead.classList.add('article-head');
  articleHead.innerHTML = `${article.post_date} : ${article.article_content.slice(0, 30)}...`;
  const articleMsg = document.createElement('p');
  articleMsg.innerHTML = 'Click to edit article';
  articleEl.appendChild(articleHead);
  articleEl.appendChild(articleMsg);
  return articleEl;
};

export const createArticleEls = async (card) => {
  const articles = await getArticles(card);
  if (articles.length === 0) return null;
  if (card.content_type.substr(0, 5) !== 'multi') articles.length = 1;
  const articlesEl = document.createElement('section');
  articlesEl.classList.add('articles');
  for (let article of articles) {
    const articleEl = await createArticleEl(article);
    if (articleEl) {
      articleEl.addEventListener('click', async (e) => {
        editArticle(article, card);
      });
      articlesEl.appendChild(articleEl);
    }
  }
  return articlesEl;
};
