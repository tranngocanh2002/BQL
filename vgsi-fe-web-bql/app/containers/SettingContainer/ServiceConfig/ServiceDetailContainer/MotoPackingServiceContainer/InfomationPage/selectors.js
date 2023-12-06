import { createSelector } from "reselect";
import { initialState } from "./reducer";

/**
 * Direct selector to the infomationMotoPackingPage state domain
 */

const selectInfomationMotoPackingPageDomain = state =>
  state.get("infomationMotoPackingPage", initialState);

/**
 * Other specific selectors
 */

/**
 * Default selector used by InfomationMotoPackingPage
 */

const makeSelectInfomationMotoPackingPage = () =>
  createSelector(selectInfomationMotoPackingPageDomain, substate => substate.toJS());

export default makeSelectInfomationMotoPackingPage;
export { selectInfomationMotoPackingPageDomain };
