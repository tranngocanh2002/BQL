/*
 * PermissionDeniedPage Messages
 *
 * This contains all the text for the PermissionDeniedPage container.
 */

import { defineMessages } from 'react-intl';

export const scope = 'app.containers.PermissionDeniedPage';

export default defineMessages({
  header: {
    id: `${scope}.header`,
    defaultMessage: 'This is the PermissionDeniedPage container!',
  },
  desc: {
    id: `${scope}.desc`,
    defaultMessage: 'Không có quyền truy cập',
  },
  backText: {
    id: `${scope}.backText`,
    defaultMessage: 'Trở lai',
  },
});
