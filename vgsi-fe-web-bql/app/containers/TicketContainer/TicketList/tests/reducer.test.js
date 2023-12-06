import { fromJS } from 'immutable';
import ticketListReducer from '../reducer';

describe('ticketListReducer', () => {
  it('returns the initial state', () => {
    expect(ticketListReducer(undefined, {})).toEqual(fromJS({}));
  });
});
