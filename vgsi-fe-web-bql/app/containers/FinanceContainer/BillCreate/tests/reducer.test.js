import { fromJS } from 'immutable';
import billCreateReducer from '../reducer';

describe('billCreateReducer', () => {
  it('returns the initial state', () => {
    expect(billCreateReducer(undefined, {})).toEqual(fromJS({}));
  });
});
