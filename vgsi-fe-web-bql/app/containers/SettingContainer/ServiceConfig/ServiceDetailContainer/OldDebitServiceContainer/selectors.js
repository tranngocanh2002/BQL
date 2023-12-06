import { createSelector } from "reselect";
import { initialState } from "./reducer";

/**
 * Direct selector to the oldDebitServiceContainer state domain
 */

const selectOldDebitServiceContainerDomain = state =>
  state.get("oldDebitServiceContainer", initialState);

/**
 * Other specific selectors
 */

/**
 * Default selector used by OldDebitServiceContainer
 */

const makeSelectOldDebitServiceContainer = () =>
  createSelector(selectOldDebitServiceContainerDomain, substate =>
    substate.toJS()
  );

export default makeSelectOldDebitServiceContainer;
export { selectOldDebitServiceContainerDomain };
