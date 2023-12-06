import { createSelector } from "reselect";
import { initialState } from "./reducer";

/**
 * Direct selector to the notificationDetail state domain
 */

const selectNotificationDetailDomain = state =>
  state.get("notificationDetail", initialState);

/**
 * Other specific selectors
 */

/**
 * Default selector used by NotificationDetail
 */

const makeSelectNotificationDetail = () =>
  createSelector(selectNotificationDetailDomain, substate => substate.toJS());

export default makeSelectNotificationDetail;
export { selectNotificationDetailDomain };
