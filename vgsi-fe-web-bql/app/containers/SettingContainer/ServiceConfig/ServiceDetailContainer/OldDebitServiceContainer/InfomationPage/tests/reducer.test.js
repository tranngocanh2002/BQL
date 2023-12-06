import { fromJS } from 'immutable';
import infomationOldDebitPageReducer from '../reducer';

describe('infomationOldDebitPageReducer', () => {
  it('returns the initial state', () => {
    expect(infomationOldDebitPageReducer(undefined, {})).toEqual(fromJS({}));
  });
});
