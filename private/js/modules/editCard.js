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

const makeArticleEl = async (card) => {
  const articles = await getArticles(card);
  let articleEl = document.createElement('textarea');
  const articleLabel = document.createElement('label');
  articleLabel.for = articleEl;
  articleLabel.innerHTML = 'article content';
  articleEl.id = 'articleContent';
  if (card.content_type.substr(0, 5) === 'multi') articleEl.placeholder = 'Enter text for new article here';
  else articleEl.innerHTML = articles[0].article_content;
  return [articleLabel, articleEl];
};

const updateCard = async (e) => {
  e.preventDefault();
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
    return res.json();
  } catch (e) {
    console.error('API error: ', e);
  }
};

export const editCard = async (card) => {
  loading.style.display = 'flex';
  loading.classList.remove('hidden');
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
  const [articleLabel, articleEl] = await makeArticleEl(card);
  console.log(articleLabel, articleEl);
  form.appendChild(articleLabel);
  form.appendChild(articleEl);
  let submitEl = document.createElement('input');
  submitEl.type = 'submit';
  submitEl.name = 'Update Card';
  submitEl.value = 'submit';
  submitEl.addEventListener('click', async (e) => {
    const res = await updateCard(e);
    console.log(res);
    document.getElementById('editForm').remove();
  });
  form.appendChild(submitEl);
  document.getElementById('content').appendChild(form);
  loading.classList.add('hidden');
  loading.style.display = 'none';
};
