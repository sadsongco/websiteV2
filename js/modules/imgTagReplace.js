const removeTagDelim = (tag) => {
  tag = tag.replace('<!--{{', '');
  tag = tag.replace('}}-->', '');
  return tag;
};

export const imgTagReplace = (content) => {
  const articleImgPath = './assets/images/article_images/';
  const tags = content.article_content.match(/<!--{{img-[0-9]}}-->/g);
  if (!tags || tags.length === 0) return content.article_content;
  for (let tag of tags) {
    const tagArr = removeTagDelim(tag).split('-');
    const imgId = tagArr[1];
    for (const img of content.images) {
      if (img.img_pos == imgId) {
        const imgURL = articleImgPath + img.url;
        const imgEl = `<img src = '${imgURL}' alt = '${img.caption}' />`;
        content.article_content = content.article_content.replace(tag, imgEl);
      }
    }
  }
  return content.article_content;
};
