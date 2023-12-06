import { fromJS } from 'immutable';
import lockFeeTemplateWaterPageReducer from '../reducer';

describe('lockFeeTemplateWaterPageReducer', () => {
  it('returns the initial state', () => {
    expect(lockFeeTemplateWaterPageReducer(undefined, {})).toEqual(fromJS({}));
  });
});
