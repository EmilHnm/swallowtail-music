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
  // Splice Object
  // IDEA
  // spliceObject(object: any, index: number, count: number, item: any) {
  //   const newObj: any = {};
  //   if (index > Object.keys(object).length || index < 0)
  //     index = Object.keys(object).length;
  //   if (count < 0) count = 0;
  //   if (count > Object.keys(object).length - index) count = 0;
  //   if (item)
  //     for (let i = 0; i <= Object.keys(object).length; i++) {
  //       if (i < index) {
  //         newObj[Object.keys(object)[i]] = Object.values(object)[i];
  //       }
  //       if (i === index) {
  //         for (let i = 0; i < Object.keys(item).length; i++) {
  //           newObj[Object.keys(item)[i]] = Object.values(item)[i];
  //         }
  //       }
  //       if (i > index + count) {
  //         newObj[Object.keys(object)[i - 1]] = Object.values(object)[i - 1];
  //       }
  //     }
  //   else {
  //     for (let i = 0; i <= Object.keys(object).length; i++) {
  //       if (i < index && i >= index + count) {
  //         newObj[Object.keys(object)[i]] = Object.values(object)[i];
  //       }
  //     }
  //   }
  //   return newObj;
  // },
  // OPTIMIZE
  spliceObject(object: any, index: number, count: number, item: any = {}) {
    //get object lenght
    const totalProps = Object.keys(object).length;
    if (index >= totalProps || index < 0)
      //if index is out of range,
      return item ? { ...object, ...item } : { ...object }; //return object with new item
    if (!count || count < 0) count = 0; // set count to 0 to prevent error
    if (count > totalProps - index) count = totalProps - index; // set count to max value if count is out of range
    const newObj = Object.keys(object).reduce((acc, key, i) => {
      if (count !== 0)
        if (i < index || i >= index + count) {
          //load all props before index and after index + count
          return { ...acc, [key]: object[key] };
        } else if (item) {
          //load new item
          return { ...acc, ...item };
        } else {
          return { ...acc };
        }
      else if (i < index || i > index + count) {
        //load all props before index and after index + count
        return { ...acc, [key]: object[key] };
      } else if (item) {
        //load new item
        return { ...acc, ...item, [key]: object[key] };
      } else {
        return { ...acc };
      }
    }, {});
    return newObj;
  },
  //suffle props in object
  shuffleObject(object: any) {
    const keys = Object.keys(object);
    const newKeys = this.shuffleArr(keys);
    const newObj = newKeys.reduce((acc, key) => {
      return { ...acc, [key]: object[key] };
    }, {});
    return newObj;
  },
  //filter Object
  filterObject(object: any, filter: string) {
    const newObj = Object.keys(object)
      .filter((key) => !filter.includes(key))
      .reduce((obj: any, key: string) => {
        obj[key] = object[key];
        return obj;
      }, {});
    return newObj;
  },
};
