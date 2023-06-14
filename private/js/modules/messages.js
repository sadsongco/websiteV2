export const insertMessage = (msg, targetId) => {
  const message = document.createElement('h1');
  message.innerHTML = msg;
  const target = document.getElementById(targetId);
  console.log(target);
  target.insertBefore(message, target.children[0]);
};

export const announceSuccess = (msg) => {
  document.getElementById('editCard').innerHTML = '';
  const successMessage = document.createElement('h1');
  successMessage.innerHTML = msg;
  document.getElementById('editCard').appendChild(successMessage);
};
