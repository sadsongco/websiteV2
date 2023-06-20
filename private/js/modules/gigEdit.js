import { createInput, createLabel } from './createFormEls.js';
import { announceSuccess, insertMessage } from './messages.js';
import { highlightErrors, createDiv } from './utilities.js';
import { addVenue } from './venueEdit.js';

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

const submitGigs = async (gigForm, target = 'submit') => {
  const gigInfoSets = gigForm.getElementsByClassName('gigInfoSet');
  const requiredFields = ['date[]', 'venue[]'];
  const errorFields = [];
  for (const gigInfoSet of gigInfoSets) {
    for (const gigInfo of gigInfoSet.children) {
      if (gigInfo.tagName === 'LEGEND' || gigInfo.type === 'submit') continue;
      if (requiredFields.includes(gigInfo.name) && gigInfo.value === '') errorFields.push(gigInfo.id);
    }
  }
  if (errorFields.length > 0) return { success: false, error: 'validation', errorFields: errorFields };
  const data = new FormData(gigForm);
  const url = `./API/${target}Gigs.php`;
  try {
    const res = await fetch(url, {
      method: 'POST',
      body: data,
    });
    return await res.json();
  } catch (e) {
    return { success: false, error: 'API error: ' + e };
  }
};

const updateGig = async (gigForm) => {
  return await submitGigs(gigForm, 'update');
};

const deleteGig = async (gigForm) => {
  const id = parseInt(gigForm.id.split('_')[1]);
  const url = './API/deleteGig.php';
  try {
    const res = await fetch(url, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({ gig_id: id }),
    });
    return await res.json();
  } catch (e) {
    return { success: false, error: 'API error: ' + e };
  }
};

const gigEditSubmit = async (gigForm, gigDelete = false) => {
  let res;
  if (gigDelete) {
    res = await deleteGig(gigForm);
    if (res.success) {
      res.message = 'gig deleted';
      return res;
    }
    return res;
  }
  res = await updateGig(gigForm);
  if (res.success) {
    return res;
  }
  return res;
};

/* **** CREATE GIG EDIT ELEMENTS **** */

const addSubmitGigs = (gigForm) => {
  let submitParams = { type: 'submit', name: 'submitGigs', value: 'submit gigs' };
  const submit = createInput(submitParams);
  submit.classList.add('gigSubmit');
  submit.id = 'gigSubmit';
  submit.addEventListener('click', async (e) => {
    e.preventDefault();
    const res = await submitGigs(gigForm);
    if (res.success) {
      announceSuccess('gigs inserted into database');
      return;
    }
    if (res.error === 'validation') highlightErrors(res.errorFields, 'gigForm');
  });
  return submit;
};

const addGigButton = (gigForm) => {
  // add another gig
  const submit = createInput({ type: 'submit', name: 'addGig', value: 'add another gig' });
  submit.addEventListener('click', async (e) => {
    e.preventDefault();
    e.target.remove();
    const gigSubmit = document.getElementById('gigSubmit');
    gigSubmit.remove();
    const newGig = await createGigInput(++gigIndex, gigForm);
    newGig.appendChild(submit);
    gigForm.appendChild(newGig);
    gigForm.appendChild(e.target);
    gigForm.appendChild(gigSubmit);
  });
  return submit;
};

const addVenueButton = (gigInput) => {
  // add venue
  const createVenue = createInput({ type: 'submit', name: 'addVenue', value: 'add a new venue' });
  createVenue.addEventListener('click', (e) => {
    e.preventDefault();
    addVenue();
  });
  gigInput.appendChild(createVenue);
};

const addRemoveGig = (gigInput) => {
  // add remove gig if not only gig input
  if (gigInput.id === 'gigInfoSet_1') return;
  const removeGig = createInput({ type: 'submit', name: 'removeGig', value: 'remove gig' });
  removeGig.addEventListener('click', (e) => {
    e.preventDefault();
    e.target.parentNode.remove();
  });
  gigInput.appendChild(removeGig);
};

const addGigId = (gigId) => {
  return createInput({ type: 'hidden', name: 'gigId', value: gigId });
};

const addUpdateGig = (gigForm) => {
  const submit = createInput({ type: 'submit', name: 'updateGig', value: 'update gig' });
  submit.addEventListener('click', async (e) => {
    e.preventDefault();
    const res = await gigEditSubmit(gigForm);
    document.getElementById('editArticle').classList.remove('modal-open');
    if (res.success) {
      announceSuccess('gig updated');
      return;
    }
    insertMessage('there was an error: ' + res.error, 'editCard');
  });
  return submit;
};

const addDeleteGig = (gigForm) => {
  const deleteGig = createInput({ type: 'submit', name: 'deleteGig', value: 'delete gig' });
  deleteGig.addEventListener('click', async (e) => {
    e.preventDefault();
    const res = await gigEditSubmit(gigForm, true);
    document.getElementById('editArticle').classList.remove('modal-open');
    if (res.success) {
      announceSuccess('gig deleted');
      return;
    }
    insertMessage('there was an error: ' + res.error, 'editCard');
  });
  return deleteGig;
};

const addCancel = () => {
  const cancelEditing = createInput({ type: 'submit', name: 'cancel', value: 'cancel' });
  cancelEditing.addEventListener('click', (e) => {
    e.preventDefault();
    document.getElementById('editArticle').classList.remove('modal-open');
    return;
  });
  return cancelEditing;
};

const createGigInput = async (gigIndex, gigForm, gigInfo = null) => {
  const res = await fetch('./API/getVenues.php');
  const venues = await res.json();
  const venueOptions = [];
  for (const venue of venues) {
    venueOptions.push({ id: venue.venue_id, name: `${venue.name} ${venue.city}, ${venue.country}` });
  }
  const options = {
    selected: gigInfo ? gigInfo.venue_id : null,
    options: venueOptions,
  };
  const inputs = [
    {
      type: 'date',
      id: `date_${gigIndex}`,
      name: `date[]`,
      placeholder: 'gig date',
    },
    {
      type: 'select',
      id: `venue_${gigIndex}`,
      name: `venue[]`,
      options: options,
    },
    {
      type: 'text',
      id: `tickets_${gigIndex}`,
      name: `tickets[]`,
      placeholder: 'ticket link',
    },
  ];
  if (gigInfo) {
    inputs[0].value = gigInfo.date;
    inputs[2].value = gigInfo.tickets;
    // TODO sort selected venue for editing
  }
  const gigInput = document.createElement('fieldset');
  gigInput.classList.add('gigInfoSet');
  gigInput.id = `gigInfoSet_${gigIndex}`;
  const gigLegend = document.createElement('legend');
  gigLegend.innerHTML = `gig details - new show ${gigIndex}`;
  if (gigInfo) gigLegend.innerHTML = `edit gig - ${gigInfo.date} at ${gigInfo.venue}, ${gigInfo.city}`;
  gigInput.appendChild(gigLegend);
  for (const input of inputs) {
    const inputEl = createInput(input);
    gigInput.appendChild(inputEl);
  }
  if (!gigInfo) {
    addVenueButton(gigInput);
    addRemoveGig(gigInput);
  } else {
    gigInput.appendChild(addGigId(gigInfo.gig_id));
    gigInput.appendChild(addUpdateGig(gigForm));
    gigInput.appendChild(addDeleteGig(gigForm));
    gigInput.appendChild(addCancel());
  }
  return gigInput;
};

const createGigEdit = (gig) => {
  const gigEditEl = document.createElement('div');
  gigEditEl.classList.add('gigEdit');
  gigEditEl.appendChild(createDiv('gigInfo', gig.date));
  gigEditEl.appendChild(createDiv('gigInfo', gig.venue));
  gigEditEl.appendChild(createDiv('gigInfo', gig.city));
  gigEditEl.appendChild(createDiv('gigInfo', gig.country));
  const editGigButton = document.createElement('button');
  editGigButton.innerHTML = 'edit gig';
  editGigButton.addEventListener('click', (e) => {
    editGig(gig);
  });
  gigEditEl.appendChild(createDiv('gigInfo', editGigButton));
  return gigEditEl;
};

const editGig = async (gig) => {
  const editGigForm = await createGigsForm(true, gig.gig_id);
  editGigForm.appendChild(await createGigInput(gigIndex, editGigForm, gig));
  const editArticleContainer = document.getElementById('editArticle');
  editArticleContainer.innerHTML = '';
  editArticleContainer.appendChild(editGigForm);
  editArticleContainer.classList.add('modal-open');
  editArticleContainer.scrollIntoView({ block: 'start', inline: 'start' });
};

export const createGigsEdit = async (past = false) => {
  const gigsEditContainer = document.createElement('section');
  gigsEditContainer.classList.add('gigsEdit');
  const gigsEditHead = document.createElement('h1');
  gigsEditHead.innerHTML = past ? 'edit gig history' : 'edit upcoming gigs';
  gigsEditContainer.appendChild(gigsEditHead);
  const gigList = await getGigs(past);
  for (let gig of gigList) {
    gigsEditContainer.appendChild(createGigEdit(gig));
  }
  return gigsEditContainer;
};

// ***** EXPORT ***** //

// global gig index
var gigIndex = 1;

export const createGigsForm = async (edit = null, gigId = null) => {
  // form for new gigs
  const gigForm = document.createElement('form');
  gigForm.id = 'gigForm';
  if (gigId) gigForm.id = `gigEditForm_${gigId}`;
  gigForm.method = 'post';
  gigForm.action = '#';
  if (!edit) {
    gigForm.appendChild(await createGigInput(gigIndex, gigForm));
    gigForm.appendChild(addGigButton(gigForm));
    gigForm.appendChild(addSubmitGigs(gigForm));
    return gigForm;
  }
  return gigForm;
};
