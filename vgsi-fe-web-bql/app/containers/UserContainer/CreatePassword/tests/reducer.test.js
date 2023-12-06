import { fromJS } from 'immutable';
import createPasswordReducer from '../reducer';

describe('createPasswordReducer', () => {
  it('returns the initial state', () => {
    expect(createPasswordReducer(undefined, {})).toEqual(fromJS({}));
  });
});
