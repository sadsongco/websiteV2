const postData = async (url = '', data = {}) => {
  try {
    const response = await fetch(url, {
      method: 'POST', // *GET, POST, PUT, DELETE, etc.
      mode: 'cors', // no-cors, *cors, same-origin
      cache: 'no-cache', // *default, no-cache, reload, force-cache, only-if-cached
      credentials: 'same-origin', // include, *same-origin, omit
      headers: {
        'Content-Type': 'application/json',
        // 'Content-Type': 'application/x-www-form-urlencoded',
      },
      redirect: 'follow', // manual, *follow, error
      referrerPolicy: 'no-referrer', // no-referrer, *no-referrer-when-downgrade, origin, origin-when-cross-origin, same-origin, strict-origin, strict-origin-when-cross-origin, unsafe-url
      body: JSON.stringify(data), // body data type must match "Content-Type" header
    });
    console.log(JSON.stringify(data));
    console.log(response);
    return response.json(); // parses JSON response into native JavaScript objects
  } catch (e) {
    console.log('API error: ' + e);
  }
};

const contentForm = document.getElementById('content-update-form');
let data = {};
const apiURL = 'admin.php';
contentForm.addEventListener('submit', (e) => {
  e.preventDefault();
  const inputs = contentForm.querySelectorAll('input');
  for (let input of inputs) {
    data[input.id] = e.target[input.id].value;
  }
  const textareas = contentForm.querySelectorAll('textarea');
  for (let textarea of textareas) {
    data[textarea.id] = e.target[textarea.id].value;
  }
  const apiReturn = postData(apiURL, data);
  console.log(apiReturn);
});
