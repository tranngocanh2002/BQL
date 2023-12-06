import { createSelector } from 'reselect';
import { initialState } from './reducer';

/**
 * Direct selector to the rolesCreate state domain
 */

const selectRolesCreateDomain = state => state.get('rolesCreate', initialState);

/**
 * Other specific selectors
 */

/**
 * Default selector used by RolesCreate
 */

const makeSelectRolesCreate = () =>
  createSelector(selectRolesCreateDomain, substate => substate.toJS());

export default makeSelectRolesCreate;
export { selectRolesCreateDomain };
