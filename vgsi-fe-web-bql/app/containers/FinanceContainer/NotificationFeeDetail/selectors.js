import { createSelector } from "reselect";
import { initialState } from "./reducer";

/**
 * Direct selector to the notificationFeeDetail state domain
 */

const selectNotificationFeeDetailDomain = state =>
  state.get("notificationFeeDetail", initialState);

/**
 * Other specific selectors
 */

/**
 * Default selector used by NotificationFeeDetail
 */

const makeSelectNotificationFeeDetail = () =>
  createSelector(selectNotificationFeeDetailDomain, substate => substate.toJS());

export default makeSelectNotificationFeeDetail;
export { selectNotificationFeeDetailDomain };
