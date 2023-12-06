import { createSelector } from "reselect";
import { initialState } from "./reducer";

/**
 * Direct selector to the TaskEdit state domain
 */

const selectTaskEditDomain = (state) =>
  state.get("TaskEdit", initialState);

/**
 * Other specific selectors
 */

/**
 * Default selector used by TaskEdit
 */

const makeSelectTaskEdit = () =>
  createSelector(selectTaskEditDomain, (substate) => substate.toJS());

export default makeSelectTaskEdit;
export { selectTaskEditDomain };
