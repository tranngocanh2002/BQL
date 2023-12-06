import { fromJS } from 'immutable';
import historyAccessControlReducer from '../reducer';

describe('historyAccessControlReducer', () => {
  it('returns the initial state', () => {
    expect(historyAccessControlReducer(undefined, {})).toEqual(fromJS({}));
  });
});
