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

export const createEditCardForm = () => {
  if (document.getElementById('editForm')) document.getElementById('editForm').remove();
  const form = document.createElement('form');
  form.id = 'editForm';
  form.method = 'post';
  form.action = '#';
  return form;
};

export const createEditCardFormInputs = (form, card) => {
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
};

export const createEditCardFormSubmit = (form) => {
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
};
