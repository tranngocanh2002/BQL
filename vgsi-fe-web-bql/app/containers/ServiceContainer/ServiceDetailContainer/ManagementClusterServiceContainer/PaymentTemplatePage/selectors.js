import { createSelector } from "reselect";
import { initialState } from "./reducer";
import { selectManagementClusterServiceContainerDomain } from "../selectors";

/**
 * Direct selector to the paymentTemplateManagementClusterPage state domain
 */

const selectPaymentTemplateManagementClusterPageDomain = state => state.get("paymentTemplateManagementClusterPage", initialState);

/**
 * Other specific selectors
 */

/**
 * Default selector used by PaymentTemplateManagementClusterPage
 */



const makeSelectManagementClusterServiceContainer = () => createSelector(
  (state, props) => props.managementClusterServiceContainer,
  selectManagementClusterServiceContainerDomain,
  (substateProps, substateState) => substateProps || substateState.toJS()
)


const makeSelectPaymentTemplateManagementClusterPage = () =>
  createSelector(selectPaymentTemplateManagementClusterPageDomain, substate => substate.toJS());

export default makeSelectPaymentTemplateManagementClusterPage;
export { selectPaymentTemplateManagementClusterPageDomain, makeSelectManagementClusterServiceContainer };
