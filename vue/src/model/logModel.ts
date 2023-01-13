export interface log {
  [index: string]: {
    context: string;
    level: "error" | "emergency" | "info";
    folder?: string;
    level_class: "danger" | "warning" | "info";
    level_img: "info-circle" | "exclamation-triangle";
    date: string;
    text: string;
    in_file?: string;
    stack: string;
  };
}
