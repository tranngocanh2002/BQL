import { fromJS } from 'immutable';
import billListReducer from '../reducer';

describe('billListReducer', () => {
  it('returns the initial state', () => {
    expect(billListReducer(undefined, {})).toEqual(fromJS({}));
  });
});
