import { fromJS } from 'immutable';
import lockFeeTemplateElectricPageReducer from '../reducer';

describe('lockFeeTemplateElectricPageReducer', () => {
  it('returns the initial state', () => {
    expect(lockFeeTemplateElectricPageReducer(undefined, {})).toEqual(fromJS({}));
  });
});
