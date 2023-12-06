import { fromJS } from 'immutable';
import CashBookReducer from '../reducer';

describe('CashBookReducer', () => {
  it('returns the initial state', () => {
    expect(CashBookReducer(undefined, {})).toEqual(fromJS({}));
  });
});
