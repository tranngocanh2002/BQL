import { createSelector } from "reselect";
import { initialState } from "./reducer";

/**
 * Direct selector to the combineCardDetail state domain
 */

const selectCombineCardDetailDomain = (state) =>
  state.get("combineCardDetail", initialState);

/**
 * Other specific selectors
 */

/**
 * Default selector used by CombineCardDetail
 */

const makeSelectCombineCardDetail = () =>
  createSelector(selectCombineCardDetailDomain, (substate) => substate.toJS());
export default makeSelectCombineCardDetail;
export { selectCombineCardDetailDomain };
