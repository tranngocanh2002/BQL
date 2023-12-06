import { fromJS } from 'immutable';
import infomationElectricPageReducer from '../reducer';

describe('infomationElectricPageReducer', () => {
  it('returns the initial state', () => {
    expect(infomationElectricPageReducer(undefined, {})).toEqual(fromJS({}));
  });
});
