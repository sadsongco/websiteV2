export const createInput = (params) => {
  const input = params.type == 'textarea' ? document.createElement('textarea') : document.createElement('input');
  if (params.id) input.id = params.id;
  input.name = params.name;
  input.addEventListener('input', (e) => {
    e.target.classList.remove('error');
  });
  if (params.type == 'textarea') {
    input.innerHTML = params.value;
    return input;
  }
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
