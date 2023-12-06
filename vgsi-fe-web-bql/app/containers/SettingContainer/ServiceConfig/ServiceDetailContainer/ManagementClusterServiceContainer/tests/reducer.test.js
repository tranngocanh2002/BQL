import { fromJS } from 'immutable';
import managementClusterServiceContainerReducer from '../reducer';

describe('managementClusterServiceContainerReducer', () => {
  it('returns the initial state', () => {
    expect(managementClusterServiceContainerReducer(undefined, {})).toEqual(fromJS({}));
  });
});
