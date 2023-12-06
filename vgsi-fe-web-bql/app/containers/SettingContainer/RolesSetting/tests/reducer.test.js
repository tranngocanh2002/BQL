import { fromJS } from 'immutable';
import rolesCreateReducer from '../reducer';

describe('rolesCreateReducer', () => {
  it('returns the initial state', () => {
    expect(rolesCreateReducer(undefined, {})).toEqual(fromJS({}));
  });
});
