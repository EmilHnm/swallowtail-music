import type { Pagination } from "@/model/mixin/PaginateDataModel";

export interface notification {
  id: string;
  type: string;
  notifiable_type: string;
  notifiable_id: string;
  data: {
    title: string;
    message: string;
    icon: string;
    link: {
      name: string;
      params:
        | {
            [key: string]: string | number;
          }
        | string;
    };
  };
  read_at: string;
  created_at: string;
  updated_at: string;
}

export interface notificationPagination extends Pagination {
  data: notification[];
}
