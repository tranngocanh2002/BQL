import { createSelector } from "reselect";
import { initialState } from "./reducer";

/**
 * Direct selector to the SetupFeeMotoPackingPage state domain
 */

const selectSetupFeeMotoPackingPageDomain = state =>
  state.get("SetupFeeMotoPackingPage", initialState);

/**
 * Other specific selectors
 */

/**
 * Default selector used by SetupFeeMotoPackingPage
 */

const makeSelectSetupFeeMotoPackingPage = () =>
  createSelector(selectSetupFeeMotoPackingPageDomain, substate => substate.toJS());

export default makeSelectSetupFeeMotoPackingPage;
export { selectSetupFeeMotoPackingPageDomain };
