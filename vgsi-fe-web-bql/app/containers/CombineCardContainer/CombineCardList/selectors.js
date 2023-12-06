import { createSelector } from "reselect";
import { initialState } from "./reducer";

/**
 * Direct selector to the combineCardList state domain
 */

const selectCombineCardListDomain = (state) =>
  state.get("combineCardList", initialState);

/**
 * Other specific selectors
 */

/**
 * Default selector used by CombineCardList
 */

const makeSelectCombineCardList = () =>
  createSelector(selectCombineCardListDomain, (substate) => substate.toJS());

export default makeSelectCombineCardList;
export { selectCombineCardListDomain };
