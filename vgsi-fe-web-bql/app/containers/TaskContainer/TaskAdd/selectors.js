import { createSelector } from "reselect";
import { initialState } from "./reducer";

/**
 * Direct selector to the TaskAdd state domain
 */

const selectTaskAddDomain = (state) =>
  state.get("TaskAdd", initialState);

/**
 * Other specific selectors
 */

/**
 * Default selector used by TaskAdd
 */

const makeSelectTaskAdd = () =>
  createSelector(selectTaskAddDomain, (substate) => substate.toJS());

export default makeSelectTaskAdd;
export { selectTaskAddDomain };
