import { fromJS } from 'immutable';
import notificationCategoryReducer from '../reducer';

describe('notificationCategoryReducer', () => {
  it('returns the initial state', () => {
    expect(notificationCategoryReducer(undefined, {})).toEqual(fromJS({}));
  });
});
