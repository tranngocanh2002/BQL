import { createSelector } from "reselect";
import { initialState } from "./reducer";

/**
 * Direct selector to the SetupFeeElectricPage state domain
 */

const selectSetupFeeElectricPageDomain = state =>
  state.get("SetupFeeElectricPage", initialState);

/**
 * Other specific selectors
 */

/**
 * Default selector used by SetupFeeElectricPage
 */

const makeSelectSetupFeeElectricPage = () =>
  createSelector(selectSetupFeeElectricPageDomain, substate => substate.toJS());

export default makeSelectSetupFeeElectricPage;
export { selectSetupFeeElectricPageDomain };
