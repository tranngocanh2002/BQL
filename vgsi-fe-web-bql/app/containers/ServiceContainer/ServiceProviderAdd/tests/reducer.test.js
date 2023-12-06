import { fromJS } from 'immutable';
import serviceProviderAddReducer from '../reducer';

describe('serviceProviderAddReducer', () => {
  it('returns the initial state', () => {
    expect(serviceProviderAddReducer(undefined, {})).toEqual(fromJS({}));
  });
});
