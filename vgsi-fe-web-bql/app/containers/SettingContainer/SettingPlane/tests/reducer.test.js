import { fromJS } from 'immutable';
import settingPlaneReducer from '../reducer';

describe('settingPlaneReducer', () => {
  it('returns the initial state', () => {
    expect(settingPlaneReducer(undefined, {})).toEqual(fromJS({}));
  });
});
