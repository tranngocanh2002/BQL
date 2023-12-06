import { fromJS } from 'immutable';
import serviceFeeCreateReducer from '../reducer';

describe('serviceFeeCreateReducer', () => {
  it('returns the initial state', () => {
    expect(serviceFeeCreateReducer(undefined, {})).toEqual(fromJS({}));
  });
});
