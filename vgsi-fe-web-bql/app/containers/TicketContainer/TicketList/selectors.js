import { createSelector } from "reselect";
import { initialState } from "./reducer";

/**
 * Direct selector to the ticketList state domain
 */

const selectTicketListDomain = state => state.get("ticketList", initialState);

/**
 * Other specific selectors
 */

/**
 * Default selector used by TicketList
 */

const makeSelectTicketList = () =>
  createSelector(selectTicketListDomain, substate => substate.toJS());

export default makeSelectTicketList;
export { selectTicketListDomain };
