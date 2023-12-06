import { fromJS } from 'immutable';
import resourceManagerReducer from '../reducer';

describe('resourceManagerReducer', () => {
  it('returns the initial state', () => {
    expect(resourceManagerReducer(undefined, {})).toEqual(fromJS({}));
  });
});
