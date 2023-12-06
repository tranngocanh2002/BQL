import { fromJS } from 'immutable';
import lockFeePageReducer from '../reducer';

describe('lockFeePageReducer', () => {
  it('returns the initial state', () => {
    expect(lockFeePageReducer(undefined, {})).toEqual(fromJS({}));
  });
});
