const submitNewArticle = async (e) => {
  const data = [];
  let target = e.target.name;
  for (let el of e.target.parentElement.children) {
    if (el.tagName == 'INPUT' || el.tagName == 'TEXTAREA') {
      const obj = {};
      obj[el.name] = el.value;
      data.push(obj);
    }
  }
  try {
    if (target === '') return null;
    const res = await fetch(`./API/${target}Article.php`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(data),
    });
    // return await res.text();
    return await res.json();
  } catch (e) {
    console.error('API error: ', e);
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
  ];
  const form = createNewArticleForm(card, { id: 'editArticle', method: 'post', action: '#' }, inputs);
  const editArticleContainer = document.getElementById('editArticle');
  editArticleContainer.innerHTML = '';
  editArticleContainer.appendChild(form);
};

const createInput = (params) => {
  const input = params.type == 'textarea' ? document.createElement('textarea') : document.createElement('input');
  if (params.id) input.id = params.id;
  input.name = params.name;
  if (params.type == 'textarea') {
    input.innerHTML = params.value;
    return input;
  }
  input.type = params.type;
  if (params.placeholder) input.placeholder = params.placeholder;
  if (params.value) input.value = params.value;
  return input;
};

const createLabel = (params) => {
  const label = document.createElement('label');
  label.for = params.for;
  label.innerHTML = params.text;
  return label;
};

export const createNewArticleForm = (card, formParams, inputs) => {
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
    if ((input.type = 'submit')) {
      inputEl.addEventListener('click', async (e) => {
        e.preventDefault();
        const res = await submitNewArticle(e);
        console.log(res);
      });
    }
  }
  return form;
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
      //   articleEl.addEventListener('click', editArticle(article));
      articleEl.addEventListener('click', async (e) => {
        const res = await editArticle(article, card);
      });
      articlesEl.appendChild(articleEl);
    }
  }
  return articlesEl;
};
