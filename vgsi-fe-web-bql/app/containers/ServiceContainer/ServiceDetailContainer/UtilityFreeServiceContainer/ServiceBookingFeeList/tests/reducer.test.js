import { fromJS } from 'immutable';
import serviceBookingFeeListReducer from '../reducer';

describe('serviceBookingFeeListReducer', () => {
  it('returns the initial state', () => {
    expect(serviceBookingFeeListReducer(undefined, {})).toEqual(fromJS({}));
  });
});
