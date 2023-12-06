import { createSelector } from "reselect";
import { initialState } from "./reducer";

/**
 * Direct selector to the serviceList state domain
 */

const selectServiceListDomain = state => state.get("serviceListCustom", initialState);

/**
 * Other specific selectors
 */

/**
 * Default selector used by ServiceList
 */

const makeSelectServiceList = () =>
  createSelector(selectServiceListDomain, substate => substate.toJS());

export default makeSelectServiceList;
export { selectServiceListDomain };
