import { fromJS } from 'immutable';
import billDetailReducer from '../reducer';

describe('billDetailReducer', () => {
  it('returns the initial state', () => {
    expect(billDetailReducer(undefined, {})).toEqual(fromJS({}));
  });
});
