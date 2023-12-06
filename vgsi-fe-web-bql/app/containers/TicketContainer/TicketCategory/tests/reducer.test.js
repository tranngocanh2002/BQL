import { fromJS } from 'immutable';
import ticketCategoryReducer from '../reducer';

describe('ticketCategoryReducer', () => {
  it('returns the initial state', () => {
    expect(ticketCategoryReducer(undefined, {})).toEqual(fromJS({}));
  });
});
