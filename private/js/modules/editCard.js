const loading = document.getElementById('loading');

const createEditForm = () => {
  if (document.getElementById('editForm')) document.getElementById('editForm').remove();
  const form = document.createElement('form');
  form.id = 'editForm';
  form.method = 'post';
  form.action = '#';
  return form;
};

const getInputType = (key) => {
  switch (key) {
    case 'bg_colour':
    case 'bg_image':
    case 'card_id':
    case 'card_pos':
    case 'content_type':
      return 'hidden';
      break;
    default:
      return 'text';
  }
};

const getArticles = async (card) => {
  let content_type = card.content_type.substr(0, 5) === 'multi' ? 'multi' : 'single';
  const url = `./API/getArticles.php?card_id=${card.card_id}&content_type=${content_type}`;
  const articlesArr = await fetch(url);
  return articlesArr.json();
};

const submitNewArticle = async (e) => {
  const data = [];
  console.log(e.target.parentElement);
  for (let el of e.target.parentElement.children) {
    if (el.tagName == 'INPUT' || el.tagName == 'TEXTAREA') {
      const obj = {};
      obj[el.name] = el.value;
      data.push(obj);
    }
  }
  console.log(data);
};

const makeNewArticleEl = (card) => {
  console.log(card);
  const form = document.createElement('form');
  form.id = 'newArticle';
  form.method = 'post';
  form.action = '#';
  const articleContent = document.createElement('textarea');
  articleContent.id = 'articleContent';
  articleContent.name = 'article_content';
  articleContent.placeholder = 'Enter your article content here';
  const contentLabel = document.createElement('label');
  contentLabel.for = 'articleContent';
  contentLabel.innerHTML = 'create new article';
  form.appendChild(contentLabel);
  form.appendChild(articleContent);
  const liveDate = document.createElement('input');
  liveDate.type = 'datetime-local';
  liveDate.id = 'liveDate';
  const dateLabel = document.createElement('label');
  dateLabel.for = 'liveDate';
  dateLabel.innerHTML = 'enter date for article to go live (if nothing entered will go live immediately)';
  form.appendChild(dateLabel);
  form.appendChild(liveDate);
  const cardId = document.createElement('input');
  cardId.type = 'hidden';
  cardId.name = 'card_id';
  cardId.value = card.card_id;
  const submit = document.createElement('input');
  submit.type = 'submit';
  submit.name = 'submit';
  submit.value = 'upload new article';
  submit.addEventListener('click', async (e) => {
    e.preventDefault();
    const res = await submitNewArticle(e);
    console.log(res);
  });
  form.appendChild(submit);
  return form;
};

const makeArticleEls = async (card) => {
  const articles = await getArticles(card);
  if (articles.length === 0) return null;
  if (card.content_type.substr(0, 5) !== 'multi') articles.length = 1;
  const articlesEl = document.createElement('section');
  articlesEl.classList.add('articles');
  for (let article of articles) {
    const articleEl = await makeArticleEl(article);
    if (articleEl) articlesEl.appendChild(articleEl);
  }
  return articlesEl;
};

const makeArticleEl = async (article) => {
  const articleEl = document.createElement('section');
  articleEl.addEventListener('click', editArticle(article));
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

const editArticle = async (article) => {
  console.log(article);
};

const updateCard = async (e) => {
  const data = [];
  for (let el of e.target.parentElement.children) {
    if (el.tagName == 'INPUT') {
      const obj = {};
      obj[el.name] = el.value;
      data.push(obj);
    }
  }
  try {
    const res = await fetch('./API/updateCard.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(data),
    });
    return await res.text();
    return await res.json();
  } catch (e) {
    console.error('API error: ', e);
  }
};

export const editCard = async (card) => {
  loading.style.display = 'flex';
  loading.classList.remove('hidden');

  // container for card editing
  const container = document.getElementById('editCard');

  // card edit form
  const form = createEditForm();
  for (let [key, value] of Object.entries(card)) {
    const inputType = getInputType(key);
    if (inputType != 'hidden') {
      const labelEl = document.createElement('label');
      labelEl.for = key;
      labelEl.innerHTML = key;
      form.appendChild(labelEl);
    }
    const inputEl = document.createElement('input');
    inputEl.id = key;
    inputEl.type = inputType;
    inputEl.name = key;
    inputEl.value = value;
    inputEl.size = 30;
    form.appendChild(inputEl);
    inputType != 'hidden' ? form.appendChild(document.createElement('br')) : null;
  }
  let submitEl = document.createElement('input');
  submitEl.type = 'submit';
  submitEl.name = 'Update Card';
  submitEl.value = 'Update Card';
  submitEl.addEventListener('click', async (e) => {
    e.preventDefault();
    const res = await updateCard(e);
    console.log(res);
    document.getElementById('editForm').remove();
  });
  form.appendChild(submitEl);
  container.appendChild(form);

  // new article form
  const newArticleEl = makeNewArticleEl(card);
  container.appendChild(newArticleEl);

  // edit old articles
  const articlesEl = await makeArticleEls(card);
  if (articlesEl) container.appendChild(articlesEl);

  // hide loading
  loading.classList.add('hidden');
  loading.style.display = 'none';
};
