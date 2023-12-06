import { fromJS } from 'immutable';
import apartmentAddReducer from '../reducer';

describe('apartmentAddReducer', () => {
  it('returns the initial state', () => {
    expect(apartmentAddReducer(undefined, {})).toEqual(fromJS({}));
  });
});
