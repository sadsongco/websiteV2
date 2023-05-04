export const animateOnScroll = () => {
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(
      (entry) => {
        if (entry.isIntersecting) {
          // console.log(`added show from ${entry.target.id}`);
          entry.target.classList.add('show');
        } else {
          // console.log(`removed show from ${entry.target.id}`);
          entry.target.classList.remove('show');
        }
      },
      { threshold: 0 }
    );
  });
  const cards = document.querySelectorAll('.card-content');
  cards.forEach((el) => {
    observer.observe(el);
  });
};
