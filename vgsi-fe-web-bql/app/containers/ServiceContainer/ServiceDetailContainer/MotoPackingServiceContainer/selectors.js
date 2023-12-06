import { createSelector } from "reselect";
import { initialState } from "./reducer";

/**
 * Direct selector to the motoPackingServiceContainer state domain
 */

const selectMotoPackingServiceContainerDomain = state =>
  state.get("motoPackingServiceContainer", initialState);

/**
 * Other specific selectors
 */

/**
 * Default selector used by MotoPackingServiceContainer
 */

const makeSelectMotoPackingServiceContainer = () =>
  createSelector(selectMotoPackingServiceContainerDomain, substate =>
    substate.toJS()
  );

export default makeSelectMotoPackingServiceContainer;
export { selectMotoPackingServiceContainerDomain };
