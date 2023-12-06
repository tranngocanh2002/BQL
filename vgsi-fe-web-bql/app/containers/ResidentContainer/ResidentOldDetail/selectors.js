import { createSelector } from "reselect";
import { initialState } from "./reducer";

/**
 * Direct selector to the residentOldDetail state domain
 */

const selectResidentOldDetailDomain = state =>
  state.get("residentOldDetail", initialState);

/**
 * Other specific selectors
 */

/**
 * Default selector used by ResidentOldDetail
 */

const makeSelectResidentOldDetail = () =>
  createSelector(selectResidentOldDetailDomain, substate => substate.toJS());

export default makeSelectResidentOldDetail;
export { selectResidentOldDetailDomain };
