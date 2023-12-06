import { fromJS } from 'immutable';
import dashboardInvoiceBillDetailReducer from '../reducer';

describe('dashboardInvoiceBillDetailReducer', () => {
  it('returns the initial state', () => {
    expect(dashboardInvoiceBillDetailReducer(undefined, {})).toEqual(fromJS({}));
  });
});
