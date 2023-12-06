import { fromJS } from 'immutable';
import infomationManagementClusterPageReducer from '../reducer';

describe('infomationManagementClusterPageReducer', () => {
  it('returns the initial state', () => {
    expect(infomationManagementClusterPageReducer(undefined, {})).toEqual(fromJS({}));
  });
});
