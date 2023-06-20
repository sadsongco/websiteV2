import { createGigsForm, createGigsEdit } from './gigEdit.js';

/* **** APPLICATION **** */

export const createGigEditView = async (card) => {
  const gigEditView = document.createElement('section');
  gigEditView.id = 'gigEditView';
  gigEditView.classList.add('gigEditView');
  // form for new gigs
  const gigForm = await createGigsForm();
  // addNewGigInput(gigForm, gigIndex);
  gigEditView.appendChild(gigForm);
  gigEditView.appendChild(await createGigsEdit());
  // edit gig history
  gigEditView.appendChild(await createGigsEdit(true));
  return gigEditView;
};
