import { fromJS } from 'immutable';
import notificationUpdateReducer from '../reducer';

describe('notificationUpdateReducer', () => {
  it('returns the initial state', () => {
    expect(notificationUpdateReducer(undefined, {})).toEqual(fromJS({}));
  });
});
