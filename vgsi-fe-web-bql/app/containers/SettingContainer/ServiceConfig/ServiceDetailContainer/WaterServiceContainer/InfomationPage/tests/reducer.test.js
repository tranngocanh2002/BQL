import { fromJS } from 'immutable';
import infomationWaterPageReducer from '../reducer';

describe('infomationWaterPageReducer', () => {
  it('returns the initial state', () => {
    expect(infomationWaterPageReducer(undefined, {})).toEqual(fromJS({}));
  });
});
