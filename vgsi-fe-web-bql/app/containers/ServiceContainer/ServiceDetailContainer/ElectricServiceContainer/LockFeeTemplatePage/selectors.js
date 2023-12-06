import { createSelector } from "reselect";
import { initialState } from "./reducer";
import { selectElectricServiceContainerDomain } from "../selectors";

/**
 * Direct selector to the lockFeeTemplateElectricPage state domain
 */

const selectLockFeeTemplatePageDomain = (state, props) => {
  return state.get("lockFeeTemplateElectricPage", initialState)
};

/**
 * Other specific selectors
 */

/**
 * Default selector used by LockFeeTemplatePage
 */

const makeSelectElectricServiceContainer = () => createSelector(
  (state, props) => props.electricServiceContainer,
  selectElectricServiceContainerDomain,
  (substateProps, substateState) => substateProps || substateState.toJS()
)


const makeSelectLockFeeTemplatePage = () =>
  createSelector(selectLockFeeTemplatePageDomain, substate => substate.toJS());

export default makeSelectLockFeeTemplatePage;
export { selectLockFeeTemplatePageDomain, makeSelectElectricServiceContainer };
