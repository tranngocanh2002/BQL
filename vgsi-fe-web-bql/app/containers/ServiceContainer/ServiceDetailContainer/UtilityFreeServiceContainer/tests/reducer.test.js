import { fromJS } from 'immutable';
import utilityFreeServiceContainerReducer from '../reducer';

describe('utilityFreeServiceContainerReducer', () => {
  it('returns the initial state', () => {
    expect(utilityFreeServiceContainerReducer(undefined, {})).toEqual(fromJS({}));
  });
});
