export const loadBackgroundImage = async (src) => {
  return new Promise((resolve, reject) => {
    const image = new Image();
    image.addEventListener('load', resolve);
    image.addEventListener('error', reject);
    image.src = src;
    return image;
  });
};
