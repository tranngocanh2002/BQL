import { createSelector } from "reselect";
import { initialState } from "./reducer";
import { selectWaterServiceContainerDomain } from "../selectors";

/**
 * Direct selector to the lockFeeTemplateWaterPage state domain
 */

const selectLockFeeTemplatePageDomain = (state, props) => {
  return state.get("lockFeeTemplateWaterPage", initialState)
};

/**
 * Other specific selectors
 */

/**
 * Default selector used by LockFeeTemplatePage
 */

const makeSelectWaterServiceContainer = () => createSelector(
  (state, props) => props.waterServiceContainer,
  selectWaterServiceContainerDomain,
  (substateProps, substateState) => substateProps || substateState.toJS()
)


const makeSelectLockFeeTemplatePage = () =>
  createSelector(selectLockFeeTemplatePageDomain, substate => substate.toJS());

export default makeSelectLockFeeTemplatePage;
export { selectLockFeeTemplatePageDomain, makeSelectWaterServiceContainer };
