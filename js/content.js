// grab loader element to use where needed
const loader = document.getElementById('loading');

// load background images and show page

const loadBackgroundImage = async (src) => {
  return new Promise((resolve, reject) => {
    const image = new Image();
    image.addEventListener('load', resolve);
    image.addEventListener('error', reject);
    image.src = src;
    return image;
  });
};

const placeBackgroundImage = async (src, target) => {
  target.style.backgroundAttachment = 'fixed';
  target.style.backgroundSize = 'cover';
  target.style.backgroundImage = `url(${src})`;
};

const buildBackgrounds = async () => {
  const backgrounds = {
    about: 'https://picsum.photos/1000',
    album: 'https://picsum.photos/800',
    shows: 'https://picsum.photos/900',
    other: 'https://picsum.photos/950',
  };
  const cards = document.getElementsByClassName('card');
  let backgroundImagePromises = [];
  for (const card of cards) {
    backgroundImagePromises.push(loadBackgroundImage(backgrounds[card.id.replace('-container', '')]));
  }
  await Promise.all(backgroundImagePromises);
  for (const backgroundKey in backgrounds) {
    const target = document.getElementById(`${backgroundKey}-container`);
    await placeBackgroundImage(backgrounds[backgroundKey], target);
  }
  document.getElementById('initial-load').style.display = 'none';
  loader.style.display = 'none';
};

// load page content and display

const showLoading = async (targetId) => {
  const newLoader = loader.cloneNode(true);
  newLoader.id = `${targetId}-loader`;
  newLoader.style.display = 'inline-block';
  const target = document.getElementById(`${targetId}-text`);
  target.innerHTML = '';
  target.appendChild(newLoader);
};

const updateContent = async (apiData) => {
  for (let entry of apiData) {
    const contentTarget = document.getElementById(`${entry.target}-text`);
    contentTarget.innerHTML = entry.content.replace(/\n/g, '<br />');
  }
};
const buildPage = async () => {
  await showLoading('other');
  await showLoading('about');
  await showLoading('album');
  fetch('./API/home.php')
    .then((res) => res.json())
    .then((data) => updateContent(data));
};

// run the page
buildBackgrounds();
buildPage();
