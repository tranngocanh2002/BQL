import { fromJS } from 'immutable';
import residentHandbookReducer from '../reducer';

describe('residentHandbookReducer', () => {
  it('returns the initial state', () => {
    expect(residentHandbookReducer(undefined, {})).toEqual(fromJS({}));
  });
});
