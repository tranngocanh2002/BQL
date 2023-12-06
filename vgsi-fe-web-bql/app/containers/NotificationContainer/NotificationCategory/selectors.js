import { createSelector } from "reselect";
import { initialState } from "./reducer";

/**
 * Direct selector to the notificationCategory state domain
 */

const selectNotificationCategoryDomain = state =>
  state.get("notificationCategory", initialState);

/**
 * Other specific selectors
 */

/**
 * Default selector used by NotificationCategory
 */

const makeSelectNotificationCategory = () =>
  createSelector(selectNotificationCategoryDomain, substate => substate.toJS());

export default makeSelectNotificationCategory;
export { selectNotificationCategoryDomain };
