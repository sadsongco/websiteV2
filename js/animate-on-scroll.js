const observer = new IntersectionObserver((entries) => {
  entries.forEach(
    (entry) => {
      if (entry.isIntersecting) {
        entry.target.classList.add('show');
      } else {
        entry.target.classList.remove('show');
      }
    },
    { threshold: 0 }
  );
});

const cards = document.querySelectorAll('.hidden');
cards.forEach((el) => {
  observer.observe(el);
});
