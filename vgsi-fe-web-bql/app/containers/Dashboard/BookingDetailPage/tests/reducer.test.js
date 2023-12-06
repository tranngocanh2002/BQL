import { fromJS } from 'immutable';
import bookingDetailReducer from '../reducer';

describe('bookingDetailReducer', () => {
  it('returns the initial state', () => {
    expect(bookingDetailReducer(undefined, {})).toEqual(fromJS({}));
  });
});
