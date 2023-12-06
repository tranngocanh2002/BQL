import { createSelector } from "reselect";
import { initialState } from "./reducer";

/**
 * Direct selector to the notificationFeeUpdate state domain
 */

const selectNotificationFeeUpdateDomain = state =>
  state.get("notificationFeeUpdate", initialState);

/**
 * Other specific selectors
 */

/**
 * Default selector used by NotificationFeeUpdate
 */

const makeSelectNotificationFeeUpdate = () =>
  createSelector(selectNotificationFeeUpdateDomain, substate => substate.toJS());

export default makeSelectNotificationFeeUpdate;
export { selectNotificationFeeUpdateDomain };
