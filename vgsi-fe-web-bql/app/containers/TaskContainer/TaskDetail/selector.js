import { createSelector } from "reselect";
import { initialState } from "./reducer";

const selectTaskDetailDomain = (state) => state.get("taskDetail", initialState);

const makeSelectTaskDetail = () =>
  createSelector(selectTaskDetailDomain, (substate) => substate.toJS());

export default makeSelectTaskDetail;
export { selectTaskDetailDomain };
