import { createSelector } from "reselect";
import { initialState } from "./reducer";

/**
 * Direct selector to the infomationManagementClusterPage state domain
 */

const selectInfomationManagementClusterPageDomain = state =>
  state.get("infomationManagementClusterPage", initialState);

/**
 * Other specific selectors
 */

/**
 * Default selector used by InfomationManagementClusterPage
 */

const makeSelectInfomationManagementClusterPage = () =>
  createSelector(selectInfomationManagementClusterPageDomain, substate => substate.toJS());

export default makeSelectInfomationManagementClusterPage;
export { selectInfomationManagementClusterPageDomain };
