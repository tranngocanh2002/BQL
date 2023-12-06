import { createSelector } from "reselect";
import { initialState } from "./reducer";

/**
 * Direct selector to the apartmentList state domain
 */

const selectApartmentListDomain = state =>
  state.get("apartmentList", initialState);

/**
 * Other specific selectors
 */

/**
 * Default selector used by ApartmentList
 */

const makeSelectApartmentList = () =>
  createSelector(selectApartmentListDomain, substate => substate.toJS());

export default makeSelectApartmentList;
export { selectApartmentListDomain };
