import { createSelector } from 'reselect';
import { initialState } from './reducer';

/**
 * Direct selector to the roles state domain
 */

const selectNotifySendConfigDomain = state => state.get('notifySendConfig', initialState);

/**
 * Other specific selectors
 */

/**
 * Default selector used by Roles
 */

const makeSelectNotifySendConfig = () =>
  createSelector(selectNotifySendConfigDomain, substate => substate.toJS());

export default makeSelectNotifySendConfig;
export { selectNotifySendConfigDomain };
