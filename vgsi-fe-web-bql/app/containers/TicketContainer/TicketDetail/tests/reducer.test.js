import { fromJS } from 'immutable';
import ticketDetailReducer from '../reducer';

describe('ticketDetailReducer', () => {
  it('returns the initial state', () => {
    expect(ticketDetailReducer(undefined, {})).toEqual(fromJS({}));
  });
});
