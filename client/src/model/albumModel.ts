export interface album {
  id: number;
  album_id: string;
  user_id: string;
  name: string;
  release_year: number;
  image_path: string;
  type: string;
  created_at: string;
  updated_at: string;
}

export interface AlbumForm {
  image: File;
  title: string;
  releaseYear: number;
  type: string;
  songs: {
    name: string;
    file: File;
  }[];
}
