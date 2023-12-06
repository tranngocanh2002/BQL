import { createSelector } from "reselect";
import { initialState } from "./reducer";

/**
 * Direct selector to the Form state domain
 */

const selectFormListDomain = (state) => state.get("formList", initialState);

/**
 * Other specific selectors
 */

/**
 * Default selector used by Form
 */

const makeSelectFormList = () =>
  createSelector(selectFormListDomain, (substate) => substate.toJS());

export default makeSelectFormList;
export { selectFormListDomain };
