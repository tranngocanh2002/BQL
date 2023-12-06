import { fromJS } from 'immutable';
import notificationDetailReducer from '../reducer';

describe('notificationDetailReducer', () => {
  it('returns the initial state', () => {
    expect(notificationDetailReducer(undefined, {})).toEqual(fromJS({}));
  });
});
