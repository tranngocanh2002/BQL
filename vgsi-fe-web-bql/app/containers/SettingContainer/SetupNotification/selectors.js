import { createSelector } from 'reselect';
import { initialState } from './reducer';

/**
 * Direct selector to the roles state domain
 */

const selectSetupNotificationDomain = state => state.get('setupNotification', initialState);

/**
 * Other specific selectors
 */

/**
 * Default selector used by Roles
 */

const makeSelectSetupNotification = () =>
  createSelector(selectSetupNotificationDomain, substate => substate.toJS());

export default makeSelectSetupNotification;
export { selectSetupNotificationDomain };
