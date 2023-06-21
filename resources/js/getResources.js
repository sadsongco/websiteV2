const getResources = async (resourceName) => {
  const url = `./API/get_resource.php?resource=${resourceName}`;
  const res = await fetch(url);
  const data = await res.json();
  return data;
};

const bios = await getResources('bio');
console.log(bios);
const techSpecs = await getResources('tech_spec');
console.log(techSpecs);
const photos = await getResources('press_shots');
console.log(photos);
