import { createSelector } from "reselect";
import { initialState } from "./reducer";

/**
 * Direct selector to the actionLogSystem state domain
 */

const selectActionLogSystemDomain = state =>
  state.get("actionLogSystem", initialState);

/**
 * Other specific selectors
 */

/**
 * Default selector used by ActionLogSystem
 */

const makeSelectActionLogSystem = () =>
  createSelector(selectActionLogSystemDomain, substate => substate.toJS());

export default makeSelectActionLogSystem;
export { selectActionLogSystemDomain };
