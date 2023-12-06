import { fromJS } from 'immutable';
import notificationFeeDetailReducer from '../reducer';

describe('notificationFeeDetailReducer', () => {
  it('returns the initial state', () => {
    expect(notificationFeeDetailReducer(undefined, {})).toEqual(fromJS({}));
  });
});
