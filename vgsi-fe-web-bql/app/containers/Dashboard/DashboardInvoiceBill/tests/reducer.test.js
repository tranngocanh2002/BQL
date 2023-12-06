import { fromJS } from 'immutable';
import dashboardInvoiceBillReducer from '../reducer';

describe('dashboardInvoiceBillReducer', () => {
  it('returns the initial state', () => {
    expect(dashboardInvoiceBillReducer(undefined, {})).toEqual(fromJS({}));
  });
});
