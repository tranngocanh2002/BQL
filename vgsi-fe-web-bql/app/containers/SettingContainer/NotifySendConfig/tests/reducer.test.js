import { fromJS } from 'immutable';
import notifySendConfigReducer from '../reducer';

describe('notifySendConfigReducer', () => {
  it('returns the initial state', () => {
    expect(notifySendConfigReducer(undefined, {})).toEqual(fromJS({}));
  });
});
