import { fromJS } from 'immutable';
import paymentMotoPackingPageReducer from '../reducer';

describe('paymentMotoPackingPageReducer', () => {
  it('returns the initial state', () => {
    expect(paymentMotoPackingPageReducer(undefined, {})).toEqual(fromJS({}));
  });
});
