import { fromJS } from 'immutable';
import CancelBillListReducer from '../reducer';

describe('CancelBillListReducer', () => {
  it('returns the initial state', () => {
    expect(CancelBillListReducer(undefined, {})).toEqual(fromJS({}));
  });
});
