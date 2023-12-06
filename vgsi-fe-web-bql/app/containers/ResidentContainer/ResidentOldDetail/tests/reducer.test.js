import { fromJS } from 'immutable';
import residentOldDetailReducer from '../reducer';

describe('residentOldDetailReducer', () => {
  it('returns the initial state', () => {
    expect(residentOldDetailReducer(undefined, {})).toEqual(fromJS({}));
  });
});
