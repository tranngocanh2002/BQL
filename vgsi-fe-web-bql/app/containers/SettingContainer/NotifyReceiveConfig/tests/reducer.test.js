import { fromJS } from 'immutable';
import notifyReceiveConfigReducer from '../reducer';

describe('notifyReceiveConfigReducer', () => {
  it('returns the initial state', () => {
    expect(notifyReceiveConfigReducer(undefined, {})).toEqual(fromJS({}));
  });
});
