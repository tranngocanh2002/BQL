import { fromJS } from 'immutable';
import notificationFeeUpdateReducer from '../reducer';

describe('notificationFeeUpdateReducer', () => {
  it('returns the initial state', () => {
    expect(notificationFeeUpdateReducer(undefined, {})).toEqual(fromJS({}));
  });
});
