import { fromJS } from 'immutable';
import residentGroupReducer from '../reducer';

describe('residentGroupReducer', () => {
  it('returns the initial state', () => {
    expect(residentGroupReducer(undefined, {})).toEqual(fromJS({}));
  });
});
