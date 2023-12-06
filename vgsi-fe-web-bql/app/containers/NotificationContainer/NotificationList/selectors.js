import { createSelector } from "reselect";
import { initialState } from "./reducer";

/**
 * Direct selector to the notificationList state domain
 */

const selectNotificationListDomain = state =>
  state.get("notificationList", initialState);

/**
 * Other specific selectors
 */

/**
 * Default selector used by NotificationList
 */

const makeSelectNotificationList = () =>
  createSelector(selectNotificationListDomain, substate => substate.toJS());

export default makeSelectNotificationList;
export { selectNotificationListDomain };
