import { fromJS } from 'immutable';
import notificationFeeReducer from '../reducer';

describe('notificationFeeReducer', () => {
  it('returns the initial state', () => {
    expect(notificationFeeReducer(undefined, {})).toEqual(fromJS({}));
  });
});
