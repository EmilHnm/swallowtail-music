import type { Pagination } from "./mixin/PaginateDataModel";
import type { user } from "./userModel";

export interface request {
  id: number;
  requester: string;
  type: "artist" | "genre" | "album" | "song";
  status: "pending" | "resolved" | "rejected";
  body: {
    name: string;
    description: string;
  };
  filled_by: string | user;
  user_fillable: boolean;
  created_at: string;
  updated_at: string;
}

export interface RequestResponse extends Pagination {
  data: request[];
}
