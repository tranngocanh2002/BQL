import { createSelector } from "reselect";
import { initialState } from "./reducer";

/**
 * Direct selector to the ticketDetail state domain
 */

const selectTicketDetailDomain = state =>
  state.get("ticketDetail", initialState);

/**
 * Other specific selectors
 */

/**
 * Default selector used by TicketDetail
 */

const makeSelectTicketDetail = () =>
  createSelector(selectTicketDetailDomain, substate => substate.toJS());

export default makeSelectTicketDetail;
export { selectTicketDetailDomain };
