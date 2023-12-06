import { fromJS } from 'immutable';
import lockFeeTemplatePageReducer from '../reducer';

describe('lockFeeTemplatePageReducer', () => {
  it('returns the initial state', () => {
    expect(lockFeeTemplatePageReducer(undefined, {})).toEqual(fromJS({}));
  });
});
