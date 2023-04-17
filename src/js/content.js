// grab loader element to use where needed
const loader = document.getElementById('loading');

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
    console.log(entry);
    const contentTarget = document.getElementById(`${entry.target}-text`);
    contentTarget.innerHTML = entry.content;
  }
};
const buildPage = async () => {
  await showLoading('other');
  await showLoading('about');
  fetch('./API/home.php')
    .then((res) => res.json())
    .then((data) => updateContent(data));
};

buildPage();
