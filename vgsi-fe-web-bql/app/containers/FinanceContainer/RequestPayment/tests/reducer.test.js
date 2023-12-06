import { fromJS } from 'immutable';
import requestPaymentReducer from '../reducer';

describe('requestPaymentReducer', () => {
  it('returns the initial state', () => {
    expect(requestPaymentReducer(undefined, {})).toEqual(fromJS({}));
  });
});
