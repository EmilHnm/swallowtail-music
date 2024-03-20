import type { Pagination } from "@/model/mixin/PaginateDataModel";
import type { RouteLocationRaw } from "vue-router";

export interface notification {
  id: string;
  type: string;
  notifiable_type: string;
  notifiable_id: string;
  data: {
    title: string;
    message: string;
    icon: string;
    link: RouteLocationRaw | null;
  };
  read_at: string;
  created_at: string;
  updated_at: string;
}

export interface notificationPagination extends Pagination {
  data: notification[];
}
