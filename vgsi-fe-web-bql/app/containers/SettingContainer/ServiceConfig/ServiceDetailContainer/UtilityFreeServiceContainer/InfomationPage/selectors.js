import { createSelector } from "reselect";
import { initialState } from "./reducer";

/**
 * Direct selector to the infomationUtiliityFreePage state domain
 */

const selectInfomationUtiliityFreePageDomain = state =>
  state.get("infomationUtiliityFreePage", initialState);

/**
 * Other specific selectors
 */

/**
 * Default selector used by InfomationUtiliityFreePage
 */

const makeSelectInfomationUtiliityFreePage = () =>
  createSelector(selectInfomationUtiliityFreePageDomain, substate => substate.toJS());

export default makeSelectInfomationUtiliityFreePage;
export { selectInfomationUtiliityFreePageDomain };
