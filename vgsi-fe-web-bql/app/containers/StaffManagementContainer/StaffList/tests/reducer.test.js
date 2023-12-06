import { fromJS } from 'immutable';
import staffListReducer from '../reducer';

describe('staffListReducer', () => {
  it('returns the initial state', () => {
    expect(staffListReducer(undefined, {})).toEqual(fromJS({}));
  });
});
