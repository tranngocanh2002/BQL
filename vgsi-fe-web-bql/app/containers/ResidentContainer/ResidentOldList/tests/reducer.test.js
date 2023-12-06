import { fromJS } from 'immutable';
import residentListReducer from '../reducer';

describe('residentListReducer', () => {
  it('returns the initial state', () => {
    expect(residentListReducer(undefined, {})).toEqual(fromJS({}));
  });
});
