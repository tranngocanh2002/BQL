import { fromJS } from 'immutable';
import dashboardBookingListReducer from '../reducer';

describe('dashboardBookingListReducer', () => {
  it('returns the initial state', () => {
    expect(dashboardBookingListReducer(undefined, {})).toEqual(fromJS({}));
  });
});
