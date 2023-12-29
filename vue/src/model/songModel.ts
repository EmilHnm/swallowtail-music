export interface song {
  id?: number;
  song_id: string;
  user_id: string;
  album_id: string;
  title: string;
  sub_title?: string;
  listens: number;
  duration: number;
  display: string;
  created_at: string;
  updated_at: string;
}

export type songFileUpload = {
  blob: Blob[];
  file: File;
  chunk_count: number;
  progress: number;
  song_id: string;
  status: "waiting" | "uploading" | "finish" | "error";
};
