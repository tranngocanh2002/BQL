import { fromJS } from 'immutable';
import accountBaseReducer from '../reducer';

describe('accountBaseReducer', () => {
  it('returns the initial state', () => {
    expect(accountBaseReducer(undefined, {})).toEqual(fromJS({}));
  });
});
