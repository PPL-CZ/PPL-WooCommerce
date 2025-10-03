const imagePath = (path: string) => {
  // @ts-ignore
  if (window.pplcz_data && window.pplcz_data.pluginPath) {
    const image = path.split(/\/+/).reverse()[0];
    // @ts-ignore
    const newPath = window.pplcz_data.pluginPath.replace(/\/$/) + "/media/" + image;
    return newPath;
  }
  return path;
};

export default imagePath;
