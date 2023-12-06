import { fromJS } from 'immutable';
import residentDetailReducer from '../reducer';

describe('residentDetailReducer', () => {
  it('returns the initial state', () => {
    expect(residentDetailReducer(undefined, {})).toEqual(fromJS({}));
  });
});
