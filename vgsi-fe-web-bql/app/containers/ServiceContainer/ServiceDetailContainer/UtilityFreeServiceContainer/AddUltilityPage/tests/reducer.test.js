import { fromJS } from 'immutable';
import addUltilityPageReducer from '../reducer';

describe('addUltilityPageReducer', () => {
  it('returns the initial state', () => {
    expect(addUltilityPageReducer(undefined, {})).toEqual(fromJS({}));
  });
});
