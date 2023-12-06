import { fromJS } from 'immutable';
import notificationFeeManagerReducer from '../reducer';

describe('notificationFeeManagerReducer', () => {
  it('returns the initial state', () => {
    expect(notificationFeeManagerReducer(undefined, {})).toEqual(fromJS({}));
  });
});
