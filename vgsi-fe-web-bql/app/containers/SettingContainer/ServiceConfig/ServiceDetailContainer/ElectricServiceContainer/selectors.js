import { createSelector } from "reselect";
import { initialState } from "./reducer";

/**
 * Direct selector to the electricServiceContainer state domain
 */

const selectElectricServiceContainerDomain = state =>
  state.get("electricServiceContainer", initialState);

/**
 * Other specific selectors
 */

/**
 * Default selector used by ElectricServiceContainer
 */

const makeSelectElectricServiceContainer = () =>
  createSelector(selectElectricServiceContainerDomain, substate =>
    substate.toJS()
  );

export default makeSelectElectricServiceContainer;
export { selectElectricServiceContainerDomain };
