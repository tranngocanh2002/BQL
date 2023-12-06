import { createSelector } from "reselect";

// /**
//  * Direct selector to the login state domain
//  */
const selectConfig = (state) => state.get("config").toJS();
const selectToken = () =>
  createSelector(selectConfig, (substate) => substate.token);

const selectTokenNoti = () =>
  createSelector(selectConfig, (substate) => substate.tokenNoti);

const selectBuildingCluster = () =>
  createSelector(selectConfig, (substate) => substate.buildingCluster);
// const selectEnvironmentSensor = () => createSelector(
//     selectConfig,
//     (substate) => substate.environment
// );
export {
  selectToken,
  selectTokenNoti,
  // selectEnvironmentSensor,
  selectBuildingCluster,
};
