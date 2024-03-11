const scrollToLink = () => {
  const params = new URLSearchParams(document.location.search);
  const page = params.get('page');
  if (page != '') document.getElementById(page).scrollIntoView({ behavior: 'smooth' });
};
