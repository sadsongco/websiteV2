const validEmail = (mail) => {
  if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(mail)) {
    return true;
  }
  return false;
};

const submitEmail = async (form) => {
  const apiURL = './api/email_subscribe.php';
  const email = document.getElementById('email').value;
  const name = document.getElementById('name').value;
  const postObj = { email: email, name: name };
  try {
    const res = await fetch(apiURL, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(postObj),
    });
    return res.json();
  } catch (err) {
    console.error(err);
  }
};

const submit = document.getElementById('emailSubmit');
submit.disabled = true;

document.getElementById('emailList').addEventListener('submit', async (e) => {
  e.preventDefault();
  submit.disabled = true;
  submit.value = '... adding email';
  const res = await submitEmail(e.target);
  if (res.status == 'db_error') {
    submit.value = 'there was an error, please try again';
    submit.disable = false;
    return;
  }
  if (res.status == 'exists') {
    submit.value = `you're already on the list!`;
  }
  if (res.success) {
    submit.value = 'thank you! check email to confirm';
  }
  document.getElementById('email').disabled = true;
  document.getElementById('name').disabled = true;
});

document.getElementById('email').addEventListener('input', (e) => {
  if (validEmail(e.target.value)) {
    submit.disabled = false;
    submit.value = 'join the list';
  } else {
    submit.disabled = true;
    submit.value = 'enter your email';
  }
});
