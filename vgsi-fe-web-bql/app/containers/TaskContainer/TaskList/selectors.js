import { createSelector } from "reselect";
import { initialState } from "./reducer";

/**
 * Direct selector to the taskList state domain
 */

const selectTaskListDomain = state => state.get("taskList", initialState);

/**
 * Other specific selectors
 */

/**
 * Default selector used by TaskList
 */

const makeSelectTaskList = () =>
  createSelector(selectTaskListDomain, substate => substate.toJS());

export default makeSelectTaskList;
export { selectTaskListDomain };
