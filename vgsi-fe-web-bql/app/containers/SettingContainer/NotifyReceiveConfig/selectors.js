import { createSelector } from 'reselect';
import { initialState } from './reducer';

/**
 * Direct selector to the roles state domain
 */

const selectNotifyReceiveConfigDomain = state => state.get('notifyReceiveConfig', initialState);

/**
 * Other specific selectors
 */

/**
 * Default selector used by Roles
 */

const makeSelectNotifyReceiveConfig = () =>
  createSelector(selectNotifyReceiveConfigDomain, substate => substate.toJS());

export default makeSelectNotifyReceiveConfig;
export { selectNotifyReceiveConfigDomain };
