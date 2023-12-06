import { createSelector } from "reselect";
import { initialState } from "./reducer";

/**
 * Direct selector to the infomationWaterPage state domain
 */

const selectInfomationWaterPageDomain = state =>
  state.get("infomationWaterPage", initialState);

/**
 * Other specific selectors
 */

/**
 * Default selector used by InfomationWaterPage
 */

const makeSelectInfomationWaterPage = () =>
  createSelector(selectInfomationWaterPageDomain, substate => substate.toJS());

export default makeSelectInfomationWaterPage;
export { selectInfomationWaterPageDomain };
