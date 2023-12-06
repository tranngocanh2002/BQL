import { fromJS } from 'immutable';
import paymentManagementClusterPageReducer from '../reducer';

describe('paymentManagementClusterPageReducer', () => {
  it('returns the initial state', () => {
    expect(paymentManagementClusterPageReducer(undefined, {})).toEqual(fromJS({}));
  });
});
