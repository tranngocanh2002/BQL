import { fromJS } from 'immutable';
import SetupFeeWaterPageReducer from '../reducer';

describe('SetupFeeWaterPageReducer', () => {
  it('returns the initial state', () => {
    expect(SetupFeeWaterPageReducer(undefined, {})).toEqual(fromJS({}));
  });
});
