import { fromJS } from 'immutable';
import electricServiceContainerReducer from '../reducer';

describe('electricServiceContainerReducer', () => {
  it('returns the initial state', () => {
    expect(electricServiceContainerReducer(undefined, {})).toEqual(fromJS({}));
  });
});
