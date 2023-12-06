import { fromJS } from 'immutable';
import dashboardDebtAllReducer from '../reducer';

describe('dashboardDebtAllReducer', () => {
  it('returns the initial state', () => {
    expect(dashboardDebtAllReducer(undefined, {})).toEqual(fromJS({}));
  });
});
