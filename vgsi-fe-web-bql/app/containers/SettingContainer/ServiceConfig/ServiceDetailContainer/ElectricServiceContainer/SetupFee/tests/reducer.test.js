import { fromJS } from 'immutable';
import SetupFeeElectricPageReducer from '../reducer';

describe('SetupFeeElectricPageReducer', () => {
  it('returns the initial state', () => {
    expect(SetupFeeElectricPageReducer(undefined, {})).toEqual(fromJS({}));
  });
});
