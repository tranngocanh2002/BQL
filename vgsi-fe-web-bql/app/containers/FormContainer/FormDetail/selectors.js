import { createSelector } from "reselect";
import { initialState } from "./reducer";

/**
 * Direct selector to the formDetail state domain
 */

const selectFormDetailDomain = (state) => state.get("formDetail", initialState);

/**
 * Other specific selectors
 */

/**
 * Default selector used by FormDetail
 */

const makeSelectFormDetail = () =>
  createSelector(selectFormDetailDomain, (substate) => substate.toJS());

export default makeSelectFormDetail;
export { selectFormDetailDomain };
