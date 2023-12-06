import { createSelector } from "reselect";
import { initialState } from "./reducer";

/**
 * Direct selector to the infomationElectricPage state domain
 */

const selectInfomationElectricPageDomain = state =>
  state.get("infomationElectricPage", initialState);

/**
 * Other specific selectors
 */

/**
 * Default selector used by InfomationElectricPage
 */

const makeSelectInfomationElectricPage = () =>
  createSelector(selectInfomationElectricPageDomain, substate => substate.toJS());

export default makeSelectInfomationElectricPage;
export { selectInfomationElectricPageDomain };
