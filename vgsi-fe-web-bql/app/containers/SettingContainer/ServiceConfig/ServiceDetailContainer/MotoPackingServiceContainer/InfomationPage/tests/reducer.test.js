import { fromJS } from 'immutable';
import infomationMotoPackingPageReducer from '../reducer';

describe('infomationMotoPackingPageReducer', () => {
  it('returns the initial state', () => {
    expect(infomationMotoPackingPageReducer(undefined, {})).toEqual(fromJS({}));
  });
});
