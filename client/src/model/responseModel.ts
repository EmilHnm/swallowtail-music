export interface response {
  id: number;
  request_id: number;
  responder: number;
  status: "responded" | "rejected" | "approved";
  content: string;
  created_at: string;
  updated_at: string;
}
