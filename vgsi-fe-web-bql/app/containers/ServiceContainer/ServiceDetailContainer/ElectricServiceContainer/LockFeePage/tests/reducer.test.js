import { fromJS } from 'immutable';
import lockFeePagePageReducer from '../reducer';

describe('lockFeePagePageReducer', () => {
  it('returns the initial state', () => {
    expect(lockFeePagePageReducer(undefined, {})).toEqual(fromJS({}));
  });
});
