import { createSelector } from "reselect";
import { initialState } from "./reducer";

/**
 * Direct selector to the notificationFee state domain
 */

const selectNotificationFeeDomain = state =>
  state.get("notificationFee", initialState);

/**
 * Other specific selectors
 */

/**
 * Default selector used by NotificationFee
 */

const makeSelectNotificationFee = () =>
  createSelector(selectNotificationFeeDomain, substate => substate.toJS());

export default makeSelectNotificationFee;
export { selectNotificationFeeDomain };
