/*
 * ServiceCloud Messages
 *
 * This contains all the text for the ServiceCloud container.
 */

import { defineMessages } from "react-intl";

export const scope = "app.containers.ServiceCloud";

export default defineMessages({
  header: {
    id: `${scope}.header`,
    defaultMessage: "This is the ServiceCloud container!",
  },
  activeService: {
    id: `${scope}.activeService`,
    defaultMessage: "Kích hoạt dịch vụ",
  },
  activedService: {
    id: `${scope}.activedService`,
    defaultMessage: "Dịch vụ đã được kích hoạt",
  },
});
