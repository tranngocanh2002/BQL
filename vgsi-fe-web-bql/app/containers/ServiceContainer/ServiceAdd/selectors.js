import { createSelector } from "reselect";
import { initialState } from "./reducer";

/**
 * Direct selector to the serviceAdd state domain
 */

const selectServiceAddDomain = state => state.get("serviceAdd", initialState);

/**
 * Other specific selectors
 */

/**
 * Default selector used by ServiceAdd
 */

const makeSelectServiceAdd = () =>
  createSelector(selectServiceAddDomain, substate => substate.toJS());

export default makeSelectServiceAdd;
export { selectServiceAddDomain };
