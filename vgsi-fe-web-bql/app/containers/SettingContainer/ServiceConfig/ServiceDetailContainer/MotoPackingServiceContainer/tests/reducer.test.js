import { fromJS } from 'immutable';
import motoPackingServiceContainerReducer from '../reducer';

describe('motoPackingServiceContainerReducer', () => {
  it('returns the initial state', () => {
    expect(motoPackingServiceContainerReducer(undefined, {})).toEqual(fromJS({}));
  });
});
