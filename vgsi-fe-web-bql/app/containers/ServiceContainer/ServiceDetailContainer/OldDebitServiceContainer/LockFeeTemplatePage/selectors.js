import { createSelector } from "reselect";
import { initialState } from "./reducer";
import { selectOldDebitServiceContainerDomain } from "../selectors";

/**
 * Direct selector to the lockFeeTemplateOldDebitPage state domain
 */

const selectLockFeeTemplatePageDomain = (state, props) => {
  return state.get("lockFeeTemplateOldDebitPage", initialState)
};

/**
 * Other specific selectors
 */

/**
 * Default selector used by LockFeeTemplatePage
 */

const makeSelectOldDebitServiceContainer = () => createSelector(
  (state, props) => props.oldDebitServiceContainer,
  selectOldDebitServiceContainerDomain,
  (substateProps, substateState) => substateProps || substateState.toJS()
)


const makeSelectLockFeeTemplatePage = () =>
  createSelector(selectLockFeeTemplatePageDomain, substate => substate.toJS());

export default makeSelectLockFeeTemplatePage;
export { selectLockFeeTemplatePageDomain, makeSelectOldDebitServiceContainer };
