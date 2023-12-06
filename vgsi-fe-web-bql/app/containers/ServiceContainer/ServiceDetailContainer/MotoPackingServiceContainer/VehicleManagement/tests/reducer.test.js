import { fromJS } from 'immutable';
import vihicleManagementReducer from '../reducer';

describe('vihicleManagementReducer', () => {
  it('returns the initial state', () => {
    expect(vihicleManagementReducer(undefined, {})).toEqual(fromJS({}));
  });
});
