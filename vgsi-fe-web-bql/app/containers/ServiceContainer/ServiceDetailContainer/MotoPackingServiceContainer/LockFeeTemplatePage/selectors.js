import { createSelector } from "reselect";
import { initialState } from "./reducer";
import { selectMotoPackingServiceContainerDomain } from "../selectors";

/**
 * Direct selector to the lockFeeTemplatePage state domain
 */

const selectLockFeeTemplatePageDomain = state => state.get("lockFeeTemplateMotoPackingPage", initialState);

/**
 * Other specific selectors
 */

/**
 * Default selector used by LockFeeTemplatePage
 */


const makeSelectMotoPackingServiceContainer = () => createSelector(
  (state, props) => props.motoPackingServiceContainer,
  selectMotoPackingServiceContainerDomain,
  (substateProps, substateState) => substateProps || substateState.toJS()
)

const makeSelectLockFeeTemplatePage = () =>
  createSelector(selectLockFeeTemplatePageDomain, substate => substate.toJS());

export default makeSelectLockFeeTemplatePage;
export { selectLockFeeTemplatePageDomain , makeSelectMotoPackingServiceContainer};
