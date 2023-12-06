import { fromJS } from 'immutable';
import notificationAddReducer from '../reducer';

describe('notificationAddReducer', () => {
  it('returns the initial state', () => {
    expect(notificationAddReducer(undefined, {})).toEqual(fromJS({}));
  });
});
