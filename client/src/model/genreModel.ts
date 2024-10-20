import type { Pagination } from "@/model/mixin/PaginateDataModel";

export interface genre {
  genre_id: string;
  id: number;
  name: string;
  description?: string;
  created_at: null | string;
  updated_at: null | string;
}

export interface genrePagination extends Pagination {
  data: genre[];
}
