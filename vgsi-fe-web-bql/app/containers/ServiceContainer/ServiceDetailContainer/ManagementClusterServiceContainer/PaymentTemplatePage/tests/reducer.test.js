import { fromJS } from 'immutable';
import paymentTemplateManagementClusterPageReducer from '../reducer';

describe('paymentTemplateManagementClusterPageReducer', () => {
  it('returns the initial state', () => {
    expect(paymentTemplateManagementClusterPageReducer(undefined, {})).toEqual(fromJS({}));
  });
});
