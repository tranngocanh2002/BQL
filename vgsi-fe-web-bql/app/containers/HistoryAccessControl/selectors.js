import { createSelector } from "reselect";
import { initialState } from "./reducer";

/**
 * Direct selector to the historyAccessControl state domain
 */

const selecthistoryAccessControlDomain = state =>
  state.get("historyAccessControl", initialState);

/**
 * Other specific selectors
 */

/**
 * Default selector used by historyAccessControl
 */

const makeSelecthistoryAccessControl = () =>
  createSelector(selecthistoryAccessControlDomain, substate => substate.toJS());

export default makeSelecthistoryAccessControl;
export { selecthistoryAccessControlDomain };
