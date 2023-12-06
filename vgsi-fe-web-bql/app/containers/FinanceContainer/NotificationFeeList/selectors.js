import { createSelector } from "reselect";
import { initialState } from "./reducer";

/**
 * Direct selector to the notificationFeeList state domain
 */

const selectNotificationFeeListDomain = state =>
  state.get("notificationFeeList", initialState);

/**
 * Other specific selectors
 */

/**
 * Default selector used by NotificationFeeList
 */

const makeSelectNotificationFeeList = () =>
  createSelector(selectNotificationFeeListDomain, substate => substate.toJS());

export default makeSelectNotificationFeeList;
export { selectNotificationFeeListDomain };
