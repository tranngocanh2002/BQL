import { createSelector } from "reselect";
import { initialState } from "./reducer";

/**
 * Direct selector to the billCreate state domain
 */

const selectBillCreateDomain = state => state.get("billCreate", initialState);

/**
 * Other specific selectors
 */

/**
 * Default selector used by BillCreate
 */

const makeSelectBillCreate = () =>
  createSelector(selectBillCreateDomain, substate => substate.toJS());

export default makeSelectBillCreate;
export { selectBillCreateDomain };
