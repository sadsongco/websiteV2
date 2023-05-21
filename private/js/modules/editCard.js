const loading = document.getElementById('loading');

const createEditForm = () => {
  if (document.getElementById('editForm')) document.getElementById('editForm').remove();
  const form = document.createElement('form');
  form.id = 'editForm';
  form.setAttribute('method', 'post');
  form.setAttribute('action', './API/updateCard.php');
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

export const editCard = async (card) => {
  loading.style.display = 'flex';
  loading.classList.remove('hidden');
  const form = createEditForm();
  for (let [key, value] of Object.entries(card)) {
    const inputType = getInputType(key);
    if (inputType != 'hidden') {
      const labelEl = document.createElement('label');
      labelEl.setAttribute('for', key);
      labelEl.innerHTML = key;
      form.appendChild(labelEl);
    }
    const inputEl = document.createElement('input');
    inputEl.id = key;
    inputEl.setAttribute('type', inputType);
    inputEl.setAttribute('name', key);
    inputEl.setAttribute('value', value);
    inputEl.setAttribute('size', Math.min(value ? value.length : 0, 30));
    form.appendChild(inputEl);
    inputType != 'hidden' ? form.appendChild(document.createElement('br')) : null;
  }
  let submitEl = document.createElement('submit');
  submitEl.setAttribute('name', 'Update Card');
  submitEl.setAttribute('value', 'submit');
  form.appendChild(submitEl);
  document.getElementById('content').appendChild(form);
  loading.classList.add('hidden');
  loading.style.display = 'none';
};
