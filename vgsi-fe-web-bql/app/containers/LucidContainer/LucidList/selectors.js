import { createSelector } from "reselect";
import { initialState } from "./reducer";

/**
 * Direct selector to the lucidList state domain
 */

const selectLucidListDomain = state => state.get("lucidList", initialState);

/**
 * Other specific selectors
 */

/**
 * Default selector used by LucidList
 */

const makeSelectLucidList = () =>
  createSelector(selectLucidListDomain, substate => substate.toJS());

export default makeSelectLucidList;
export { selectLucidListDomain };
