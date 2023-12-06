import { createSelector } from "reselect";
import { initialState } from "./reducer";

/**
 * Direct selector to the residentDetail state domain
 */

const selectResidentDetailDomain = state =>
  state.get("residentDetail", initialState);

/**
 * Other specific selectors
 */

/**
 * Default selector used by ResidentDetail
 */

const makeSelectResidentDetail = () =>
  createSelector(selectResidentDetailDomain, substate => substate.toJS());

export default makeSelectResidentDetail;
export { selectResidentDetailDomain };
