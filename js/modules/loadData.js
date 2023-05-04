export const loadData = async () => {
  const res = await fetch('./API/content.php');
  return res.json();
};
