import { fromJS } from 'immutable';
import CancelInvoiceBillListReducer from '../reducer';

describe('CancelInvoiceBillListReducer', () => {
  it('returns the initial state', () => {
    expect(CancelInvoiceBillListReducer(undefined, {})).toEqual(fromJS({}));
  });
});
