export const _function = {
  // validate
  validateSongFileType(file: File): boolean {
    const validTypes = [
      "audio/mpeg",
      "audio/mp3",
      "audio/aac",
      "audio/ogg",
      "audio/flac",
      "audio/x-flac",
      "audio/alac",
      "audio/wav",
      "audio/aiff",
      "audio/dsd",
      "audio/pcm",
    ];
    if (validTypes.indexOf(file.type) === -1) {
      return false;
    }
    return true;
  },
  validateImageFileType(file: File): boolean {
    const validTypes = [
      "image/jpg",
      "image/jpeg",
      "image/bmp",
      "image/gif",
      "image/png",
    ];
    if (validTypes.indexOf(file.type) === -1) {
      return false;
    }
    return true;
  },
  //filter
  objectsAreSame(x: any, y: any) {
    let objectsAreSame = true;
    for (const propertyName in x) {
      if (x[propertyName] !== y[propertyName]) {
        objectsAreSame = false;
        break;
      }
    }
    return objectsAreSame;
  },
  checkIncludeString(str: string, arrayTemp: any[], endpointArr: any[]): any[] {
    arrayTemp = arrayTemp.filter((item) => {
      return !endpointArr.some((i) => i.id === item.id);
      //return !endpointArr.includes(item);
    });
    return arrayTemp.filter((item) => {
      if (item.name) return item.name.toLowerCase().includes(str.toLowerCase());
    });
  },
  // Array
  pushItemtoArray(item: any, array: any[]): void {
    array.push(item);
  },
  pushItemNotIncludedtoNewArray(item: any, array: any[], tempArr: any[]) {
    if (
      !tempArr
        .map((i) => i.name.trim().toLowerCase())
        .includes(item.trim().toLowerCase())
    )
      array.push(item);
  },
  removeItemFormArr(index: number, array: any[]) {
    array.splice(index, 1);
  },
  shuffleArr(array: Array<any>) {
    let currentIndex = array.length,
      randomIndex;
    // While there remain elements to shuffle.
    while (currentIndex != 0) {
      // Pick a remaining element.
      randomIndex = Math.floor(Math.random() * currentIndex);
      currentIndex--;
      // And swap it with the current element.
      [array[currentIndex], array[randomIndex]] = [
        array[randomIndex],
        array[currentIndex],
      ];
    }
    return array;
  },
};
