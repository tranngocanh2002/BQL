import { fromJS } from 'immutable';
import bookingAddReducer from '../reducer';

describe('bookingAddReducer', () => {
  it('returns the initial state', () => {
    expect(bookingAddReducer(undefined, {})).toEqual(fromJS({}));
  });
});
