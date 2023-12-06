import { createSelector } from "reselect";
import { initialState } from "./reducer";

/**
 * Direct selector to the supplierAdd state domain
 */

const selectSupplierAddDomain = (state) =>
  state.get("supplierAdd", initialState);

/**
 * Other specific selectors
 */

/**
 * Default selector used by SupplierAdd
 */

const makeSelectSupplierAdd = () =>
  createSelector(selectSupplierAddDomain, (substate) => substate.toJS());

export default makeSelectSupplierAdd;
export { selectSupplierAddDomain };
