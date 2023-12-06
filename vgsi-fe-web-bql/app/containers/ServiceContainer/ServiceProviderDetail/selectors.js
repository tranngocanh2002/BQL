import { createSelector } from "reselect";
import { initialState } from "./reducer";

/**
 * Direct selector to the serviceProviderDetail state domain
 */

const selectServiceProviderDetailDomain = state =>
  state.get("serviceProviderDetail", initialState);

/**
 * Other specific selectors
 */

/**
 * Default selector used by ServiceProviderDetail
 */

const makeSelectServiceProviderDetail = () =>
  createSelector(selectServiceProviderDetailDomain, substate =>
    substate.toJS()
  );

export default makeSelectServiceProviderDetail;
export { selectServiceProviderDetailDomain };
