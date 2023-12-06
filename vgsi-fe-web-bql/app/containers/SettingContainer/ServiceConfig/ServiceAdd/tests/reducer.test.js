import { fromJS } from 'immutable';
import serviceAddReducer from '../reducer';

describe('serviceAddReducer', () => {
  it('returns the initial state', () => {
    expect(serviceAddReducer(undefined, {})).toEqual(fromJS({}));
  });
});
