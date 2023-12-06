import { fromJS } from 'immutable';
import accountChangePasswordReducer from '../reducer';

describe('accountChangePasswordReducer', () => {
  it('returns the initial state', () => {
    expect(accountChangePasswordReducer(undefined, {})).toEqual(fromJS({}));
  });
});
