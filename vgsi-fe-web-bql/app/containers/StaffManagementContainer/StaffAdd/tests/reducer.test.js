import { fromJS } from 'immutable';
import staffAddReducer from '../reducer';

describe('staffAddReducer', () => {
  it('returns the initial state', () => {
    expect(staffAddReducer(undefined, {})).toEqual(fromJS({}));
  });
});
