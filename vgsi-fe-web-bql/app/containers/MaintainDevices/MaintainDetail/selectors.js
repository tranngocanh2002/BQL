import { createSelector } from "reselect";
import { initialState } from "./reducer";

/**
 * Direct selector to the MaintainDetail state domain
 */

const selectMaintainDetailDomain = (state) =>
  state.get("maintainDetail", initialState);

/**
 * Other specific selectors
 */

/**
 * Default selector used by MaintainDetail
 */

const makeSelectMaintainDetail = () =>
  createSelector(selectMaintainDetailDomain, (substate) => substate.toJS());

export default makeSelectMaintainDetail;
export { selectMaintainDetailDomain };
