import { fromJS } from 'immutable';
import dashboardDebtReducer from '../reducer';

describe('dashboardDebtReducer', () => {
  it('returns the initial state', () => {
    expect(dashboardDebtReducer(undefined, {})).toEqual(fromJS({}));
  });
});
