const typewriterMessage = ['The Exact Opposite are a band.', 'This is their web page.', 'Not much here yet, but there will be.'];

const typewriterTarget = document.getElementById('typewriter');
// clear the page
typewriterTarget.innerHTML = '';

const showLogo = () => {
  const cover = document.getElementById('cover');
  console.log(cover);
  cover.style.visibility = 'visible';
  cover.style.zIndex = 2;
  cover.style.opacity = 1;
};

let str = '<p>' + typewriterMessage.join('</p><p>') + '</p>',
  isTag,
  text,
  i = 0;

const min = 80,
  max = 160;

(type = () => {
  let wait = Math.floor(Math.random() * max) + min;
  text = str.slice(0, ++i);
  if (text === str) return showLogo();
  typewriterTarget.innerHTML = text;

  const char = text.slice(-1);
  if (char === '<') isTag = true;
  if (char === '>') isTag = false;
  if (isTag) return type();
  setTimeout(type, wait);
})();
