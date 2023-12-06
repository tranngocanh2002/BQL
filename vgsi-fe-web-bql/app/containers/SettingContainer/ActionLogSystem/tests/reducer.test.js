import { fromJS } from 'immutable';
import actionLogSystemReducer from '../reducer';

describe('actionLogSystemReducer', () => {
  it('returns the initial state', () => {
    expect(actionLogSystemReducer(undefined, {})).toEqual(fromJS({}));
  });
});
