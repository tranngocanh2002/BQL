import { createSelector } from "reselect";
import { initialState } from "./reducer";

/**
 * Direct selector to the BuildingInfomationPage state domain
 */

const selectBuildingInfomationPageDomain = state =>
  state.get("buildingInfomation", initialState);

/**
 * Other specific selectors
 */

/**
 * Default selector used by BuildingInfomationPage
 */

const makeSelectBuildingInfomationPage = () =>
  createSelector(selectBuildingInfomationPageDomain, substate => substate.toJS());

export default makeSelectBuildingInfomationPage;
export { selectBuildingInfomationPageDomain };
