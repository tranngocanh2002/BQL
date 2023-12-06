import { fromJS } from 'immutable';
import lockFeeTemplateOldDebitPageReducer from '../reducer';

describe('lockFeeTemplateOldDebitPageReducer', () => {
  it('returns the initial state', () => {
    expect(lockFeeTemplateOldDebitPageReducer(undefined, {})).toEqual(fromJS({}));
  });
});
