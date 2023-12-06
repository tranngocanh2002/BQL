import { fromJS } from 'immutable';
import serviceProviderReducer from '../reducer';

describe('serviceProviderReducer', () => {
  it('returns the initial state', () => {
    expect(serviceProviderReducer(undefined, {})).toEqual(fromJS({}));
  });
});
