export class ImageColor {
  constructor(private imgEl: HTMLImageElement) {
    imgEl.setAttribute("crossOrigin", "");
  }

  public async getAverageRGB() {
    const blockSize = 5;
    const defaultRGB = { r: 0, g: 0, b: 0 };
    const canvas = document.createElement("canvas");
    const context = canvas.getContext && canvas.getContext("2d");
    let i = -4;
    const rgb = { r: 0, g: 0, b: 0 };
    let count = 0;

    if (!context) {
      return defaultRGB;
    }

    context.drawImage(this.imgEl, 0, 0);

    return new Promise<typeof rgb>((resolve, reject) => {
      const height = (canvas.height =
        this.imgEl.naturalHeight ||
        this.imgEl.offsetHeight ||
        this.imgEl.height);
      const width = (canvas.width =
        this.imgEl.naturalWidth || this.imgEl.offsetWidth || this.imgEl.width);
      this.getContextData(context, width, height).then((data) => {
        if (data) {
          const length = data.data.length;

          while ((i += blockSize * 4) < length) {
            ++count;
            rgb.r += data.data[i];
            rgb.g += data.data[i + 1];
            rgb.b += data.data[i + 2];
          }

          rgb.r = ~~(rgb.r / count);
          rgb.g = ~~(rgb.g / count);
          rgb.b = ~~(rgb.b / count);
          resolve(rgb);
        } else {
          reject(defaultRGB);
        }
      });
    });
  }

  private async getContextData(
    context: CanvasRenderingContext2D,
    width: number,
    height: number
  ): Promise<ImageData> {
    return new Promise((resolve, reject) => {
      const image = new Image();
      image.src = this.imgEl.src;
      image.crossOrigin = "Anonymous";
      let data: ImageData | null;
      image.onload = () => {
        context.drawImage(image, 0, 0, width, height);
        data = context.getImageData(0, 0, width, height);
        console.log(data);
        resolve(data);
      };
      image.onerror = reject;
    });
  }
}
