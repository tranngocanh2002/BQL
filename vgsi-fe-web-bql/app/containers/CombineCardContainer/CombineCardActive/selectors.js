import { createSelector } from "reselect";
import { initialState } from "./reducer";

/**
 * Direct selector to the cardActive state domain
 */

const selectCombineCardActiveDomain = (state) =>
  state.get("cardActive", initialState);

/**
 * Other specific selectors
 */

/**
 * Default selector used by CombineCardActive
 */

const makeSelectCombineCardActive = () =>
  createSelector(selectCombineCardActiveDomain, (substate) => substate.toJS());

export default makeSelectCombineCardActive;
export { selectCombineCardActiveDomain };
