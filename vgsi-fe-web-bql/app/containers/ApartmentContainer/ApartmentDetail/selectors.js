import { createSelector } from "reselect";
import { initialState } from "./reducer";

/**
 * Direct selector to the apartmentDetail state domain
 */

const selectApartmentDetailDomain = (state) =>
  state.get("apartmentDetail", initialState);

/**
 * Other specific selectors
 */

/**
 * Default selector used by ApartmentDetail
 */

const makeSelectApartmentDetail = () =>
  createSelector(selectApartmentDetailDomain, (substate) => substate.toJS());
export default makeSelectApartmentDetail;
export { selectApartmentDetailDomain };
