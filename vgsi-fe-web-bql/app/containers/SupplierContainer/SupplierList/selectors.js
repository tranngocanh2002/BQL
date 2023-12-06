import { createSelector } from "reselect";
import { initialState } from "./reducer";

/**
 * Direct selector to the supplierList state domain
 */

const selectSupplierListDomain = (state) =>
  state.get("supplierList", initialState);

/**
 * Other specific selectors
 */

/**
 * Default selector used by SupplierList
 */

const makeSelectSupplierList = () =>
  createSelector(selectSupplierListDomain, (substate) => substate.toJS());

export default makeSelectSupplierList;
export { selectSupplierListDomain };
