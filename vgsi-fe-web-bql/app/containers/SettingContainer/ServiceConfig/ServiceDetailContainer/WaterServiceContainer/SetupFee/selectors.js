import { createSelector } from "reselect";
import { initialState } from "./reducer";

/**
 * Direct selector to the SetupFeeWaterPage state domain
 */

const selectSetupFeeWaterPageDomain = state =>
  state.get("SetupFeeWaterPage", initialState);

/**
 * Other specific selectors
 */

/**
 * Default selector used by SetupFeeWaterPage
 */

const makeSelectSetupFeeWaterPage = () =>
  createSelector(selectSetupFeeWaterPageDomain, substate => substate.toJS());

export default makeSelectSetupFeeWaterPage;
export { selectSetupFeeWaterPageDomain };
