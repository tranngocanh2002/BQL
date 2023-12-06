import { createSelector } from "reselect";
import { initialState } from "./reducer";

/**
 * Direct selector to the notificationUpdate state domain
 */

const selectNotificationUpdateDomain = state =>
  state.get("notificationUpdate", initialState);

/**
 * Other specific selectors
 */

/**
 * Default selector used by NotificationUpdate
 */

const makeSelectNotificationUpdate = () =>
  createSelector(selectNotificationUpdateDomain, substate => substate.toJS());

export default makeSelectNotificationUpdate;
export { selectNotificationUpdateDomain };
