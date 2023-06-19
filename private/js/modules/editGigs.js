import { createGigsForm, addNewGigInput, createGigsEdit } from './gigEdit.js';

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
  gigEditView.appendChild(await createGigsEdit());
  // edit gig history
  gigEditView.appendChild(await createGigsEdit(true));
  return gigEditView;
};
