import { fromJS } from 'immutable';
import serviceCloudReducer from '../reducer';

describe('serviceCloudReducer', () => {
  it('returns the initial state', () => {
    expect(serviceCloudReducer(undefined, {})).toEqual(fromJS({}));
  });
});
