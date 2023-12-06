import { createSelector } from "reselect";
import { initialState } from "./reducer";

/**
 * Direct selector to the notificationFeeManager state domain
 */

const selectNotificationFeeManagerDomain = state =>
  state.get("notificationFeeManager", initialState);

/**
 * Other specific selectors
 */

/**
 * Default selector used by NotificationFeeManager
 */

const makeSelectNotificationFeeManager = () =>
  createSelector(selectNotificationFeeManagerDomain, substate => substate.toJS());

export default makeSelectNotificationFeeManager;
export { selectNotificationFeeManagerDomain };
