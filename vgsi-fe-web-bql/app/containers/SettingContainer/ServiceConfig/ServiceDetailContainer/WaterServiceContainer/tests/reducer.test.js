import { fromJS } from 'immutable';
import waterServiceContainerReducer from '../reducer';

describe('waterServiceContainerReducer', () => {
  it('returns the initial state', () => {
    expect(waterServiceContainerReducer(undefined, {})).toEqual(fromJS({}));
  });
});
