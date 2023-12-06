/*
 * NotFoundPage Messages
 *
 * This contains all the text for the NotFoundPage container.
 */
import { defineMessages } from "react-intl";

export const scope = "app.containers.NotFoundPage";

export default defineMessages({
  header: {
    id: `${scope}.header`,
    defaultMessage: "This is the NotFoundPage container!",
  },
  desc: {
    id: `${scope}.desc`,
    defaultMessage: "Không tìm thấy trang",
  },
  backText: {
    id: `${scope}.backText`,
    defaultMessage: "Trở lại",
  },
});
