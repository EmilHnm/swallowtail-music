export interface request {
  id: number;
  requester: string;
  request_type: "artist" | "genre" | "album" | "song" | "system" | "interface";
  request_status: "pending" | "resolved" | "rejected";
  request_description: {
    name: string;
    description: string;
  };
  filled_by: string;
  user_fillable: boolean;
  created_at: string;
  updated_at: string;
}
