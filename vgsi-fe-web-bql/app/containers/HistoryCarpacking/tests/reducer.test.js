import { fromJS } from 'immutable';
import HistoryCarpackingReducer from '../reducer';

describe('HistoryCarpackingReducer', () => {
  it('returns the initial state', () => {
    expect(HistoryCarpackingReducer(undefined, {})).toEqual(fromJS({}));
  });
});
