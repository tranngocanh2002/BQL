import { fromJS } from 'immutable';
import feeListReducer from '../reducer';

describe('feeListReducer', () => {
  it('returns the initial state', () => {
    expect(feeListReducer(undefined, {})).toEqual(fromJS({}));
  });
});
