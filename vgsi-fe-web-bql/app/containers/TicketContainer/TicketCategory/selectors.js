import { createSelector } from "reselect";
import { initialState } from "./reducer";

/**
 * Direct selector to the ticketCategory state domain
 */

const selectTicketCategoryDomain = state =>
  state.get("ticketCategory", initialState);

/**
 * Other specific selectors
 */

/**
 * Default selector used by TicketCategory
 */

const makeSelectTicketCategory = () =>
  createSelector(selectTicketCategoryDomain, substate => substate.toJS());

export default makeSelectTicketCategory;
export { selectTicketCategoryDomain };
