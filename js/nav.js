export const buildNav = () => {
  // alert('buildNav');
  const navList = document.getElementById('nav-bar').getElementsByTagName('li');
  for (const liItem of navList) {
    liItem.onclick = () => {
      document.getElementById(liItem.getElementsByTagName('a')[0].dataset.target).scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
    };
  }
};
