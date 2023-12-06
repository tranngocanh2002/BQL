import { fromJS } from 'immutable';
import infoUsageReducer from '../reducer';

describe('infoUsageReducer', () => {
  it('returns the initial state', () => {
    expect(infoUsageReducer(undefined, {})).toEqual(fromJS({}));
  });
});
