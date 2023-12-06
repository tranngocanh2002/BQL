import { fromJS } from 'immutable';
import dashboardInvoiceListReducer from '../reducer';

describe('dashboardInvoiceListReducer', () => {
  it('returns the initial state', () => {
    expect(dashboardInvoiceListReducer(undefined, {})).toEqual(fromJS({}));
  });
});
