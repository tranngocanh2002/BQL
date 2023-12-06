import { fromJS } from 'immutable';
import dashboardBillDetailReducer from '../reducer';

describe('dashboardBillDetailReducer', () => {
  it('returns the initial state', () => {
    expect(dashboardBillDetailReducer(undefined, {})).toEqual(fromJS({}));
  });
});
