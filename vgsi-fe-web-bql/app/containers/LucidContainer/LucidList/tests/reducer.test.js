import { fromJS } from 'immutable';
import lucidListReducer from '../reducer';

describe('lucidListReducer', () => {
  it('returns the initial state', () => {
    expect(lucidListReducer(undefined, {})).toEqual(fromJS({}));
  });
});
