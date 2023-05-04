const placeBackgroundImage = async (src, target) => {
  const imageEl = document.createElement('img');
  imageEl.src = src;
  imageEl.classList.add('bg-image');
  target.appendChild(imageEl);
  target.style.backgroundAttachment = 'fixed';
  target.style.backgroundSize = 'cover';
  target.style.backgroundImage = `url(${src})`;
};

export const placeBackgroundImages = async (contentData) => {
  for (const card of contentData) {
    await placeBackgroundImage(card.bg_image, card.el);
  }
};
