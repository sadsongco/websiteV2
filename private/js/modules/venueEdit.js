import { createInput } from './createFormEls.js';
import { highlightErrors } from './utilities.js';
import { announceSuccess, insertMessage } from './messages.js';

/* **** API CALLS **** */

const submitVenue = async (venueForm) => {
  const requiredFields = ['name', 'address', 'city'];
  const errorFields = [];
  for (let inputField of venueForm) {
    if (requiredFields.includes(inputField.name) && inputField.value == '') {
      errorFields.push(inputField.id);
    }
  }
  if (errorFields.length > 0) return { success: false, error: 'validation', errorFields: errorFields };
  const data = new FormData(venueForm);
  const url = './API/submitVenue.php';
  try {
    const res = await fetch(url, {
      method: 'post',
      body: data,
    });
    return await res.json();
  } catch (e) {
    return { success: false, error: 'submitVenue API error: ' + e };
  }
};

/* **** CREATE VENUE EDIT ELEMENTS **** */

const createVenueForm = async (tourCountry = 'UK') => {
  const res = await fetch('./API/getCountries.php');
  const countries = await res.json();
  const options = {
    selected: tourCountry,
    options: countries,
  };
  const inputs = [
    {
      type: 'text',
      id: `venueName`,
      name: `name`,
      placeholder: 'venue name',
      maxlength: '64',
    },
    {
      type: 'text',
      id: `venueAddress`,
      name: `address`,
      placeholder: 'venue address',
      maxlength: '127',
    },
    {
      type: 'text',
      id: `venuePostcode`,
      name: `postcode`,
      placeholder: 'venue postcode',
      maxlength: '12',
    },
    {
      type: 'text',
      id: `venueCity`,
      name: `city`,
      placeholder: 'venue city',
      maxlength: '64',
    },
    {
      type: 'select',
      id: `country`,
      name: `country`,
      options: options,
    },
    {
      type: 'text',
      id: `venueWebsite`,
      name: `website`,
      placeholder: 'venue website',
      maxlength: '127',
    },
    {
      type: 'textarea',
      id: `venueNotes`,
      name: `notes`,
      placeholder: 'venue notes',
    },
    {
      type: 'submit',
      name: `addVenue`,
      id: 'addVenue',
      value: 'add new venue',
    },
    {
      type: 'submit',
      name: `cancel`,
      id: 'cancel',
      value: 'cancel',
    },
  ];
  const addVenueForm = document.createElement('form');
  addVenueForm.id = 'addVenue';
  for (const input of inputs) {
    const inputEl = createInput(input);
    addVenueForm.appendChild(inputEl);
    if (inputEl.id === 'addVenue') {
      inputEl.addEventListener('click', async (e) => {
        e.preventDefault();
        const res = await submitVenue(addVenueForm);
        if (res.success) {
          announceSuccess('venue inserted into database');
          document.getElementById('editArticle').classList.remove('modal-open');
          return;
        }
        if (res.error === 'validation') {
          highlightErrors(res.errorFields, addVenueForm.id);
          insertMessage('missing info: ' + res.error, addVenueForm.id);
          return;
        }
        document.getElementById('editArticle').classList.remove('modal-open');
        insertMessage('Error occurred: ' + res.error, 'editForm');
      });
    }
    if (inputEl.id === 'cancel') {
      inputEl.addEventListener('click', (e) => {
        e.preventDefault();
        document.getElementById('editArticle').classList.remove('modal-open');
      });
    }
  }
  return addVenueForm;
};

export const addVenue = async () => {
  const addVenueForm = await createVenueForm();
  const editArticleContainer = document.getElementById('editArticle');
  editArticleContainer.innerHTML = '';
  editArticleContainer.appendChild(addVenueForm);
  editArticleContainer.classList.add('modal-open');
  editArticleContainer.scrollIntoView({ block: 'start', inline: 'start' });
};
