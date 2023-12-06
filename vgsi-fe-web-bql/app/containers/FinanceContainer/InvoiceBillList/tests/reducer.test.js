import { fromJS } from 'immutable';
import invoiceBillListReducer from '../reducer';

describe('invoiceBillListReducer', () => {
  it('returns the initial state', () => {
    expect(invoiceBillListReducer(undefined, {})).toEqual(fromJS({}));
  });
});
