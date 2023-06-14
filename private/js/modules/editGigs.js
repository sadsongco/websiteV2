import { createInput, createLabel } from './createFormEls.js';
import { announceSuccess, insertMessage } from './messages.js';
import { highlightErrors } from './utilities.js';

/* **** API CALLS **** */

const getGigs = async (past = false) => {
  let url = './API/getUpcomingGigs.php';
  if (past) url = './API/getPastGigs.php';
  try {
    const res = await fetch(url);
    return await res.json();
  } catch (e) {
    return { success: false, message: e };
  }
};

const submitGigs = async (gigForm) => {
  const gigInfoSets = gigForm.getElementsByClassName('gigInfoSet');
  const postData = [];
  const errorFields = [];
  for (const gigInfoSet of gigInfoSets) {
    const gigInfoObj = {};
    for (const gigInfo of gigInfoSet.children) {
      if (gigInfo.tagName === 'LEGEND' || gigInfo.type === 'submit') continue;
      const gigNameArr = gigInfo.name.split('_');
      if (gigInfo.value === '') {
        errorFields.push(gigInfo.name);
        continue;
      }
      gigInfoObj[gigNameArr[0]] = gigInfo.value;
    }
    postData.push(gigInfoObj);
  }
  if (errorFields.length > 0) return { success: false, error: 'validation', errorFields: errorFields };
  const url = './API/submitGigs.php';
  try {
    const res = await fetch(url, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(postData),
    });
    return await res.json();
  } catch (e) {
    console.error('API error: ', e);
  }
};

const updateGig = async (gigForm) => {
  console.log(gigForm);
};

/* **** FORM CREATION **** */

const createGigInput = (gigIndex, gigForm, gigInfo) => {
  const inputs = [
    {
      type: 'date',
      id: `date_${gigIndex}`,
      name: `date_${gigIndex}`,
      placeholder: 'gig date',
    },
    {
      type: 'text',
      id: `venue_${gigIndex}`,
      name: `venue_${gigIndex}`,
      placeholder: 'venue',
    },
    {
      type: 'text',
      id: `tickets_${gigIndex}`,
      name: `tickets_${gigIndex}`,
      placeholder: 'ticket link',
    },
    {
      type: 'text',
      id: `city_${gigIndex}`,
      name: `city_${gigIndex}`,
      placeholder: 'city',
    },
    {
      type: 'text',
      id: `country_${gigIndex}`,
      name: `country_${gigIndex}`,
      placeholder: 'country',
    },
    {
      type: 'text',
      id: `address_${gigIndex}`,
      name: `address_${gigIndex}`,
      placeholder: 'venue address',
    },
  ];
  if (gigInfo) {
    inputs[0].value = gigInfo.date;
    inputs[1].value = gigInfo.venue;
    inputs[2].value = gigInfo.tickets;
    inputs[3].value = gigInfo.city;
    inputs[4].value = gigInfo.country;
    inputs[5].value = gigInfo.address;
  }
  const gigInput = document.createElement('fieldset');
  gigInput.classList.add('gigInfoSet');
  const gigLegend = document.createElement('legend');
  gigLegend.innerHTML = `gig details - new show ${gigIndex}`;
  if (gigInfo) gigLegend.innerHTML = `edit gig - ${gigInfo.date} at ${gigInfo.venue}, ${gigInfo.city}`;
  gigInput.appendChild(gigLegend);
  for (const input of inputs) {
    const inputEl = createInput(input);
    gigInput.appendChild(inputEl);
  }
  let submit;
  if (!gigInfo) {
    submit = createInput({ type: 'submit', name: 'addGig', value: 'add another gig' });
    submit.addEventListener('click', (e) => {
      e.preventDefault();
      e.target.remove();
      gigForm.appendChild(createGigInput(++gigIndex, gigForm));
    });
  } else {
    submit = createInput({ type: 'submit', name: 'updateGig', value: 'update gig' });
    submit.addEventListener('click', async (e) => {
      e.preventDefault();
      e.target.remove();
      await updateGig(gigForm);
    });
  }
  gigInput.appendChild(submit);
  return gigInput;
};

const addNewGigInput = (gigForm, gigIndex, gigInfo = null) => {
  const newGig = createGigInput(gigIndex, gigForm, gigInfo);
  gigForm.appendChild(newGig);
};

const createGigsForm = (edit = null, gigId = null) => {
  // form for new gigs
  const gigForm = document.createElement('form');
  gigForm.id = 'gigForm';
  if (gigId) gigForm.id = `gigEditForm_${gigId}`;
  gigForm.method = 'post';
  gigForm.action = './API/submitGigs.php';
  if (edit) gigForm.action = './API/updateGig.php';
  if (!edit) {
    let submitParams = { type: 'submit', name: 'submitGigs', value: 'submit gigs' };
    const submit = createInput(submitParams);
    submit.addEventListener('click', async (e) => {
      e.preventDefault();
      const res = await submitGigs(gigForm);
      if (res.success) {
        announceSuccess('gigs inserted into database');
        console.log(res);
        return;
      }
      if (res.error === 'validation') highlightErrors(res.errorFields, 'gigForm');
    });
    gigForm.appendChild(submit);
  }
  return gigForm;
};

/* **** EDIT ELEMENT CREATION **** */

const createGigEdit = (gig) => {
  const gigEditEl = document.createElement('div');
  gigEditEl.classList.add('gigEdit');
  const gigInfo = document.createElement('p');
  gigInfo.classList.add('gigInfo');
  gigInfo.innerHTML = `${gig.date} - ${gig.venue} - ${gig.city}, ${gig.country}`;
  gigEditEl.appendChild(gigInfo);
  const editGigButton = document.createElement('button');
  editGigButton.innerHTML = 'edit gig';
  editGigButton.addEventListener('click', (e) => {
    editGig(gig);
  });
  gigEditEl.appendChild(editGigButton);
  return gigEditEl;
};

const editGig = (gig) => {
  const editGigForm = createGigsForm(true, gig.gig_id);
  addNewGigInput(editGigForm, gig.date, gig);
  document.body.appendChild(editGigForm);
};

/* **** UTILITIES **** */

/* **** APPLICATION **** */

// global gigIndex
var gigIndex = 0;

export const createGigEditView = async (card) => {
  const gigEditView = document.createElement('section');
  gigEditView.id = 'gigEditView';
  gigEditView.classList.add('gigEditView');
  // form for new gigs
  const gigForm = createGigsForm();
  addNewGigInput(gigForm, gigIndex);
  ++gigIndex;
  gigEditView.appendChild(gigForm);
  // edit upcoming gigs
  const gigList = await getGigs();
  for (let gig of gigList) {
    gigEditView.appendChild(createGigEdit(gig));
  }
  // edit gig history
  const gigHistory = await getGigs(true);
  for (let gig of gigHistory) {
    gigEditView.appendChild(createGigEdit(gig));
  }
  return gigEditView;
};
