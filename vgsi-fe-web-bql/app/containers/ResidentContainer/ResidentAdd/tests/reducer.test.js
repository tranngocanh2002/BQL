import { fromJS } from 'immutable';
import residentAddReducer from '../reducer';

describe('residentAddReducer', () => {
  it('returns the initial state', () => {
    expect(residentAddReducer(undefined, {})).toEqual(fromJS({}));
  });
});
