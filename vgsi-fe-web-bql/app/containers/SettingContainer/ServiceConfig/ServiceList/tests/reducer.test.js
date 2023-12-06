import { fromJS } from 'immutable';
import serviceListReducer from '../reducer';

describe('serviceListReducer', () => {
  it('returns the initial state', () => {
    expect(serviceListReducer(undefined, {})).toEqual(fromJS({}));
  });
});
