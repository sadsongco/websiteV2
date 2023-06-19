const createOption = (option) => {
  const optionEl = document.createElement('option');
  optionEl.value = option.id;
  optionEl.innerHTML = option.name;
  return optionEl;
};

export const createInput = (params) => {
  let input;
  switch (params.type) {
    case 'textarea':
      input = document.createElement('textarea');
      if (params.name) input.name = params.name;
      if (params.value) input.innerHTML = params.value;
      if (params.placeholder) input.placeholder = params.placeholder;
      return input;
      break;
    case 'select':
      input = document.createElement('select');
      if (params.options) {
        for (const option of params.options.options) {
          const optionEl = createOption(option);
          if (params.options.selected == option.id) optionEl.selected = true;
          input.appendChild(optionEl);
        }
      }
      input.name = params.name;
      if (params.id) input.id = params.id;
      return input;
      break;
  }
  input = document.createElement('input');
  if (params.id) input.id = params.id;
  input.name = params.name;
  input.addEventListener('input', (e) => {
    e.target.classList.remove('error');
  });
  input.type = params.type;
  if (params.placeholder) input.placeholder = params.placeholder;
  if (params.value) input.value = params.value;
  return input;
};

export const createLabel = (params) => {
  const label = document.createElement('label');
  label.for = params.for;
  label.innerHTML = params.text;
  return label;
};
