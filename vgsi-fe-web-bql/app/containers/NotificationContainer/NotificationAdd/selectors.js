import { createSelector } from "reselect";
import { initialState } from "./reducer";

/**
 * Direct selector to the notificationAdd state domain
 */

const selectNotificationAddDomain = state =>
  state.get("notificationAdd", initialState);

/**
 * Other specific selectors
 */

/**
 * Default selector used by NotificationAdd
 */

const makeSelectNotificationAdd = () =>
  createSelector(selectNotificationAddDomain, substate => substate.toJS());

export default makeSelectNotificationAdd;
export { selectNotificationAddDomain };
