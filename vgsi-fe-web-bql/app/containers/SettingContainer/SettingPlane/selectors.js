import { createSelector } from "reselect";
import { initialState } from "./reducer";

/**
 * Direct selector to the settingPlane state domain
 */

const selectSettingPlaneDomain = state =>
  state.get("settingPlane", initialState);

/**
 * Other specific selectors
 */

/**
 * Default selector used by SettingPlane
 */

const makeSelectSettingPlane = () =>
  createSelector(selectSettingPlaneDomain, substate => substate.toJS());

export default makeSelectSettingPlane;
export { selectSettingPlaneDomain };
