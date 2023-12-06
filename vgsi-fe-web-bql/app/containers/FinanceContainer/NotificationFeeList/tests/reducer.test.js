import { fromJS } from 'immutable';
import notificationFeeListReducer from '../reducer';

describe('notificationFeeListReducer', () => {
  it('returns the initial state', () => {
    expect(notificationFeeListReducer(undefined, {})).toEqual(fromJS({}));
  });
});
