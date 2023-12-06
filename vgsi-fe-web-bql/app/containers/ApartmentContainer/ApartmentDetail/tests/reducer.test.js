import { fromJS } from 'immutable';
import apartmentDetailReducer from '../reducer';

describe('apartmentDetailReducer', () => {
  it('returns the initial state', () => {
    expect(apartmentDetailReducer(undefined, {})).toEqual(fromJS({}));
  });
});
