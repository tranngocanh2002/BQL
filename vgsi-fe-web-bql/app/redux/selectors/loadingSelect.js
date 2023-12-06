import { createSelector } from 'reselect';

/**
 * Direct selector to the login state domain
 */
const selectConfig = (state) => state.get('statusLoad').toJS();


const makeSelectLoading = () => createSelector(
    selectConfig,
    (substate) => substate.loading
);
const makeSelectNotificationCount = () => createSelector(
    selectConfig,
    (substate) => substate.notificationCount
);

export { selectConfig, makeSelectLoading, makeSelectNotificationCount }