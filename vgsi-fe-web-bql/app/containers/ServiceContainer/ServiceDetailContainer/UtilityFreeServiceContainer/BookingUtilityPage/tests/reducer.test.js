import { fromJS } from 'immutable';
import bookingUtilityPageReducer from '../reducer';

describe('bookingUtilityPageReducer', () => {
  it('returns the initial state', () => {
    expect(bookingUtilityPageReducer(undefined, {})).toEqual(fromJS({}));
  });
});
