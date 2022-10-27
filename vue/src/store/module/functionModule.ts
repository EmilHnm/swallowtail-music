export const functionModule = {
  // validate
  validateSongFileType(file: File): boolean {
    console.log(file);
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
  //filter
  checkIncludeString(str: string, arrayTemp: any[], endpointArr: any[]): any[] {
    arrayTemp = arrayTemp.filter((item) => !endpointArr.includes(item));
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
};
