import { fromJS } from 'immutable';
import serviceProviderDetailReducer from '../reducer';

describe('serviceProviderDetailReducer', () => {
  it('returns the initial state', () => {
    expect(serviceProviderDetailReducer(undefined, {})).toEqual(fromJS({}));
  });
});
