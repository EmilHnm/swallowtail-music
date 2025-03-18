import type { album } from "./albumModel";
import type { artist } from "./artistModel";
import type { like } from "./likeModel";

export interface song {
  id?: number;
  song_id: string;
  user_id: string;
  album_id: string;
  title: string;
  sub_title?: string;
  listens: number;
  display: string;
  created_at: string;
  updated_at: string;
  file: {
    duration: number;
    song_id: string;
  }
}

export type songFileUpload = {
  blob: Blob[];
  file: File;
  chunk_count: number;
  progress: number;
  song_id: string;
  status: "waiting" | "uploading" | "finish" | "error";
};

export type songData = song & {
  album?: album;
  artist: artist[];
  like: like[];
};
