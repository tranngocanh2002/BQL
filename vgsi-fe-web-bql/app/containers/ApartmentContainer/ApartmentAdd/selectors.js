import { createSelector } from "reselect";
import { initialState } from "./reducer";

/**
 * Direct selector to the apartmentAdd state domain
 */

const selectApartmentAddDomain = (state) =>
  state.get("apartmentAdd", initialState);

/**
 * Other specific selectors
 */

/**
 * Default selector used by ApartmentAdd
 */

const makeSelectApartmentAdd = () =>
  createSelector(selectApartmentAddDomain, (substate) => substate.toJS());
const makeSelectResidentList = () =>
  createSelector(selectApartmentAddDomain, (substate) =>
    substate.get("allResident").toJS()
  );

export default makeSelectApartmentAdd;
export { selectApartmentAddDomain, makeSelectResidentList };
