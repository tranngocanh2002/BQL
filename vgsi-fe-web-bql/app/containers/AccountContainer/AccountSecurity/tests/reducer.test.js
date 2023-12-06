import { fromJS } from 'immutable';
import accountSecurityReducer from '../reducer';

describe('accountSecurityReducer', () => {
  it('returns the initial state', () => {
    expect(accountSecurityReducer(undefined, {})).toEqual(fromJS({}));
  });
});
