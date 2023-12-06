import { fromJS } from 'immutable';
import dashboardBillsReducer from '../reducer';

describe('dashboardBillsReducer', () => {
  it('returns the initial state', () => {
    expect(dashboardBillsReducer(undefined, {})).toEqual(fromJS({}));
  });
});
