import { fromJS } from 'immutable';
import infomationUtiliityFreePageReducer from '../reducer';

describe('infomationUtiliityFreePageReducer', () => {
  it('returns the initial state', () => {
    expect(infomationUtiliityFreePageReducer(undefined, {})).toEqual(fromJS({}));
  });
});
