import { fromJS } from 'immutable';
import configUtilityPageReducer from '../reducer';

describe('configUtilityPageReducer', () => {
  it('returns the initial state', () => {
    expect(configUtilityPageReducer(undefined, {})).toEqual(fromJS({}));
  });
});
