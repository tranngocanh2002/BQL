import { createSelector } from "reselect";
import { initialState } from "./reducer";

/**
 * Direct selector to the infomationOldDebitPage state domain
 */

const selectInfomationOldDebitPageDomain = state =>
  state.get("infomationOldDebitPage", initialState);

/**
 * Other specific selectors
 */

/**
 * Default selector used by InfomationWaterPage
 */

const makeSelectInfomationWaterPage = () =>
  createSelector(selectInfomationOldDebitPageDomain, substate => substate.toJS());

export default makeSelectInfomationWaterPage;
export { selectInfomationOldDebitPageDomain };
