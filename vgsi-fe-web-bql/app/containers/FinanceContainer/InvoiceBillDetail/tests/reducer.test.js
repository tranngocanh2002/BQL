import { fromJS } from 'immutable';
import invoiceBillDetailReducer from '../reducer';

describe('invoiceBillDetailReducer', () => {
  it('returns the initial state', () => {
    expect(invoiceBillDetailReducer(undefined, {})).toEqual(fromJS({}));
  });
});
