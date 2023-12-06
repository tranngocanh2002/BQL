import { createSelector } from "reselect";

// /**
//  * Direct selector to the login state domain
//  */
// const selectConfig = (state) => state.get('hotel').toJS();
// const selectDevs = (state) => state.get('devices').toJS();
const selectWebState = (state) => state.get("webState").toJS();
// const selectStatusLoad = (state) => state.get('statusLoad').toJS();

// const selectHotelId = () => createSelector(
//     selectConfig,
//     (substate) => substate.hotel_id
// );
// const selectConnectLocal = () => createSelector(
//     selectStatusLoad,
//     (substate) => substate.connectLocal
// );
// const selectConnectCloud = () => createSelector(
//     selectStatusLoad,
//     (substate) => substate.connectCloud
// );
const selectInited = () =>
  createSelector(selectWebState, (substate) => substate.inited);
const selectToken = () =>
  createSelector(selectWebState, (substate) => substate.token);
const selectNotifications = () =>
  createSelector(selectWebState, (substate) => substate.notifications);
const selectAuthGroup = () =>
  createSelector(selectWebState, (substate) => substate.auth_group);
const selectBuildingCluster = () =>
  createSelector(selectWebState, (substate) => substate.buildingCluster);
const selectCity = () =>
  createSelector(selectWebState, (substate) => substate.city);
const selectUserDetail = () =>
  createSelector(selectWebState, (substate) => substate.userDetail);

// const selectRefreshToken = () => createSelector(
//     selectWebState,
//     (substate) => substate.token_refresh
// );
// const selectUserInfo = () => createSelector(
//     selectWebState,
//     (substate) => substate.userInfo
// );
// const selectRole = () => createSelector(
//     selectWebState,
//     (substate) => substate.role
// );

// const selectFloors = () => createSelector(
//     selectConfig,
//     (substate) => substate.floors
// );

// const selectDeviceType = () => createSelector(
//     selectConfig,
//     (substate) => substate.deviceType
// );

// const selectFloorIdx = () => createSelector(
//     selectConfig,
//     (substate) => substate.idx_Floor
// );

// const selectFloor = () => createSelector(
//     selectConfig,
//     (substate) => substate.floors
// );

// const selectDevices = () => createSelector(
//     selectDevs,
//     (substate) => substate.devices
// );

// const selectAllDevices = () => createSelector(
//     selectDevs,
//     (substate) => substate.all_devices
// );

// const makeSelectTokenSignal = () =>
//     createSelector(selectWebState, substate => substate.tokenSignal);

// export {
//     selectConfig, selectHotelId, selectFloors, selectDeviceType,
//     selectFloorIdx, selectFloor, selectDevices, selectAllDevices,
//     selectToken, selectRefreshToken, makeSelectTokenSignal,
//     selectUserInfo, selectRole, selectConnectLocal, selectConnectCloud
// }

export {
  selectInited,
  selectToken,
  selectAuthGroup,
  selectBuildingCluster,
  selectCity,
  selectUserDetail,
  selectNotifications,
};
